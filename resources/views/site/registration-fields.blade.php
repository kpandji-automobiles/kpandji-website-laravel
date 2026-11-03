{{-- Champs communs aux inscriptions SIRA et Privilège. Paramètre : prefix (clés de contenu sira.* ou priv.*). --}}
<div class="kp-form__grid">
  <x-kp.field name="name" :label="content($prefix.'.f_name')" required max="120" />
  <x-kp.field name="phone" type="tel" :label="content($prefix.'.f_phone')" required max="40" />
  <x-kp.field name="country" :label="content($prefix.'.f_country')" required max="120" :placeholder="content($prefix.'.f_country_ph')" />
  <x-kp.field name="city" :label="content($prefix.'.f_city')" required max="120" :placeholder="content($prefix.'.f_city_ph')" />
  <x-kp.field class="kp-form__wide" name="email" type="email" :label="content($prefix.'.f_email')" required max="160" />
</div>
