<header class="site-header">
    <div class="container nav">
        <a class="brand" href="{{ route('home') }}">
            <span class="hlogo">H</span> HAKLO
        </a>

        <nav class="menu">
            <div class="menu-item"><a href="/kadin" class="link">Kadın</a></div>
            <div class="menu-item"><a href="/erkek" class="link">Erkek</a></div>
            <div class="menu-item"><a href="/cocuk" class="link">Çocuk</a></div>
        </nav>

        <form class="search" role="search" onsubmit="event.preventDefault();">
            <input type="search" placeholder="Ara" aria-label="Ara">
        </form>

        {{-- 🔐 Auth menüsü --}}
        <div class="auth-menu" style="display:flex; gap:10px; align-items:center;">
            @guest
                <a href="{{ route('login') }}" class="link">Giriş yap</a>
                <a href="{{ route('register') }}" class="btn">Kayıt ol</a>
            @endguest

            @auth
                <div class="account-menu" style="position:relative;">
                    <button type="button"
                            class="link account-toggle"
                            aria-expanded="false"
                            onclick="toggleAccountMenu()">
                        👤 Hesabım
                    </button>
                    <ul id="account-dropdown"
                        class="account-dropdown"
                        hidden
                        style="position:absolute; right:0; top:120%; background:#fff; border:1px solid #eee; border-radius:10px; padding:8px; min-width:180px; box-shadow:0 10px 24px rgba(0,0,0,.06); list-style:none;">
                        <li style="padding:6px 8px;"><a class="link" href="{{ route('my.orders') }}">Siparişlerim</a></li>
                        <li style="padding:6px 8px;"><a class="link" href="{{ route('profile.edit') }}">Profil</a></li>
                        <li style="padding:6px 8px;">
                            <a href="#"
                               class="link"
                               style="color:#d00"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Çıkış
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>

        <!-- 🛒 Mini Cart -->
        <div class="mini-cart" id="miniCart">
            <button class="mini-cart__button" type="button" aria-expanded="false" onclick="toggleMiniCart()">
                🛒 Sepet <span class="mini-cart__count" id="mini-cart-item-count">0</span>
            </button>

            <div class="mini-cart__panel" id="mini-cart-panel" hidden>
                <div class="mini-cart__head">
                    Sepetiniz
                    <button type="button" class="mini-cart__close" aria-label="Kapat" onclick="toggleMiniCart()">×</button>
                </div>

                <ul class="mini-cart__items" id="mini-cart-items-list"></ul>
                <div class="mini-cart__empty" id="mini-cart-empty">Sepetiniz boş.</div>

                <div class="mini-cart__footer">
                    <div class="mini-cart__total">
                        <span id="mini-cart-total-price">₺0.00</span>
                    </div>
                    <a class="btn btn-block" href="{{ route('checkout') }}">Siparişi Tamamla</a>
                </div>
            </div>
        </div>

        <div class="mini-cart__overlay" id="mini-cart-overlay" hidden onclick="toggleMiniCart()"></div>
    </div>
</header>

<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Basit hesap menüsü aç/kapa --}}
<script>
    function toggleAccountMenu() {
        const m = document.getElementById('account-dropdown');
        if (!m) return;
        const hidden = m.hasAttribute('hidden');
        hidden ? m.removeAttribute('hidden') : m.setAttribute('hidden', true);
    }
    // Dışarı tıklayınca kapat (opsiyonel)
    document.addEventListener('click', function(e){
        const dd = document.getElementById('account-dropdown');
        const btn = document.querySelector('.account-toggle');
        if (!dd || !btn) return;
        if (!dd.contains(e.target) && !btn.contains(e.target)) {
            dd.setAttribute('hidden', true);
        }
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
