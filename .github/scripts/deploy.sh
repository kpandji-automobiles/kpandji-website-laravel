#!/usr/bin/env bash
# Déploie le dossier courant (déjà buildé, vendor inclus) sur l'hébergement cPanel, sans accès shell :
#   1. upload SFTP dans releases/<RELEASE>
#   2. liens storage et .env vers shared/
#   3. bascule atomique du lien current
#   4. finalisation (migrate, storage:link, optimize) via POST /_deploy/finalize
#   5. health-check, puis nettoyage des anciennes releases
#
# Usage : deploy.sh deploy | deploy.sh rollback
#
# Variables : DEPLOY_HOST DEPLOY_USER DEPLOY_PATH (absolu) DEPLOY_KEY_FILE DEPLOY_KNOWN_HOSTS_FILE
#             DEPLOY_HOOK_TOKEN APP_URL RELEASE (deploy uniquement) [KEEP_RELEASES=5]
set -euo pipefail

: "${DEPLOY_HOST:?}" "${DEPLOY_USER:?}" "${DEPLOY_PATH:?}" "${DEPLOY_KEY_FILE:?}" "${DEPLOY_KNOWN_HOSTS_FILE:?}"
KEEP_RELEASES="${KEEP_RELEASES:-5}"

SSH_OPTS="-i $DEPLOY_KEY_FILE -o IdentitiesOnly=yes -o BatchMode=yes -o StrictHostKeyChecking=yes -o UserKnownHostsFile=$DEPLOY_KNOWN_HOSTS_FILE"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

# Exécute un lot de commandes sftp (stdin) depuis DEPLOY_PATH. Préfixe « - » = erreur ignorée.
remote() {
    { echo "cd $DEPLOY_PATH"; cat; } > "$WORK/batch"
    # shellcheck disable=SC2086
    sftp $SSH_OPTS -q -b "$WORK/batch" "$DEPLOY_USER@$DEPLOY_HOST"
}

# Exécute des commandes lftp (upload parallèle, suppression récursive).
lftp_run() {
    lftp -c "set cmd:fail-exit yes; set net:max-retries 3; set net:timeout 30; \
        set sftp:connect-program 'ssh -a -x $SSH_OPTS'; \
        open -u '$DEPLOY_USER,' sftp://$DEPLOY_HOST; cd $DEPLOY_PATH; $1"
}

# Release actuellement en ligne (écrite après chaque déploiement réussi).
live_release() {
    remote <<< "-get .current_release $WORK/current_release" > /dev/null 2>&1 || true
    [ -f "$WORK/current_release" ] && tr -d '[:space:]' < "$WORK/current_release" || true
}

point_current_to() {
    remote <<EOF
-rm current.new
ln -s releases/$1 current.new
rename current.new current
EOF
}

finalize() {
    local code
    for attempt in $(seq 1 30); do
        code="$(curl -sS -o "$WORK/finalize.json" -w '%{http_code}' -X POST \
            -H "Authorization: Bearer $DEPLOY_HOOK_TOKEN" -H 'Accept: application/json' \
            --data-urlencode "release=$RELEASE" "$APP_URL/_deploy/finalize")" || code=000
        case "$code" in
            200) cat "$WORK/finalize.json"; echo; return 0 ;;
            # 409 : PHP sert encore l'ancienne release (cache realpath) ; 404/5xx passerelle : idem pendant la bascule.
            409|404|000|502|503|504) echo "Finalisation : HTTP $code, nouvel essai ($attempt/30)…"; sleep 10 ;;
            *) echo "Finalisation en échec (HTTP $code) :"; cat "$WORK/finalize.json"; echo; return 1 ;;
        esac
    done
    echo "La release $RELEASE n'est toujours pas servie après 5 minutes."
    return 1
}

prune() {
    local live="$1" previous="$2"
    remote <<< "ls -1 releases" | grep -E '^releases/[0-9]{14}-[0-9a-f]{7}$' | sed 's#^releases/##' | sort > "$WORK/releases" || true
    local total
    total="$(wc -l < "$WORK/releases" | tr -d ' ')"
    [ "$total" -le "$KEEP_RELEASES" ] && return 0
    head -n "$((total - KEEP_RELEASES))" "$WORK/releases" | while read -r old; do
        [ "$old" = "$live" ] || [ "$old" = "$previous" ] && continue
        echo "Suppression de l'ancienne release $old"
        lftp_run "rm -r releases/$old"
    done
}

deploy() {
    : "${RELEASE:?}" "${DEPLOY_HOOK_TOKEN:?}" "${APP_URL:?}"
    local previous
    previous="$(live_release)"
    echo "Release en ligne : ${previous:-aucune} → nouvelle release : $RELEASE"

    echo "Upload des fichiers…"
    lftp_run "mirror --reverse --parallel=8 --verbose=0 . releases/$RELEASE"

    echo "Liens partagés et bascule de current…"
    remote <<EOF
ln -s $DEPLOY_PATH/shared/storage releases/$RELEASE/storage
ln -s $DEPLOY_PATH/shared/.env releases/$RELEASE/.env
EOF
    point_current_to "$RELEASE"

    echo "Finalisation (migrate, storage:link, optimize)…"
    finalize

    echo "Health-check $APP_URL/up…"
    curl -fsS -o /dev/null --retry 3 --retry-delay 5 --retry-all-errors "$APP_URL/up"

    echo "$RELEASE" > "$WORK/new_release"
    remote <<< "put $WORK/new_release .current_release" > /dev/null
    prune "$RELEASE" "$previous"
    echo "Déploiement $RELEASE terminé."
}

rollback() {
    local previous
    previous="$(live_release)"
    if [ -z "$previous" ]; then
        echo "Aucune release précédente : rien à restaurer."
        return 0
    fi
    echo "Retour à la release $previous"
    point_current_to "$previous"
}

case "${1:-}" in
    deploy) deploy ;;
    rollback) rollback ;;
    *) echo "Usage : $0 deploy|rollback" >&2; exit 64 ;;
esac
