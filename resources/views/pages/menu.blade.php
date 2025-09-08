<header class="site-header">
    <div class="container nav">
        <a class="brand" href="{{ route('home') }}">
            <span class="hlogo">H</span> HAKLO
        </a>

        <nav class="menu">
            <div class="menu-item">
                <a href="/kadin" class="link">Kadın</a>
            </div>
            <div class="menu-item">
                <a href="/erkek" class="link">Erkek</a>
            </div>
            <div class="menu-item">
                <a href="/cocuk" class="link">Çocuk</a>
            </div>
        </nav>

        <form class="search" role="search" onsubmit="event.preventDefault();">
            <input type="search" placeholder="Ara" aria-label="Ara">
        </form>

        <div class="mini-cart" id="miniCart">
            <button class="mini-cart__button" type="button" aria-expanded="false">
                🛒 Sepet <span class="mini-cart__count" id="mini-cart-item-count">0</span>
            </button>

            <div class="mini-cart__panel" hidden>
                <div class="mini-cart__head">
                    Sepetiniz
                    <button type="button" class="mini-cart__close" aria-label="Kapat">×</button>
                </div>

                <ul class="mini-cart__items" id="mini-cart-items-list"></ul>
                <div class="mini-cart__empty">Sepetiniz boş.</div>

                <div class="mini-cart__footer">
                    <div class="mini-cart__total" id="mini-cart-total-container">
                        <span id="mini-cart-total-price">₺0.00</span>
                    </div>
                    <a class="btn btn-block" href="{{ route('checkout') }}">Siparişi Tamamla</a>
                </div>
            </div>
        </div>

        <div class="mini-cart__overlay" hidden></div>

</header>
