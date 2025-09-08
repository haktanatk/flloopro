<?php
// ---- Basit sepet sistemi (SESSION) ----
session_start();

// Sepet dizi yapısı: ['id'=>['ad'=>..., 'fiyat'=>..., 'adet'=>..., 'img'=>...]]
if (!isset($_SESSION['sepet'])) { $_SESSION['sepet'] = []; }

// Demo ürün listesi (normalde DB'den gelir)
$urunler = [
    ['id'=>101, 'ad'=>'Desen Çanta',     'fiyat'=>1199.90, 'img'=>'img/p1.jpg'],
    ['id'=>102, 'ad'=>'Spor Ayakkabı',   'fiyat'=>1599.00, 'img'=>'img/p2.jpg'],
    ['id'=>103, 'ad'=>'Hasır Şapka',     'fiyat'=>349.90,  'img'=>'img/p3.jpg'],
    ['id'=>104, 'ad'=>'Güneş Gözlüğü',   'fiyat'=>899.00,  'img'=>'img/p4.jpg'],
    ['id'=>105, 'ad'=>'Sırt Çantası',    'fiyat'=>999.00,  'img'=>'img/p5.jpg'],
    ['id'=>106, 'ad'=>'Koşu Tişörtü',    'fiyat'=>279.90,  'img'=>'img/p6.jpg'],
];

// Ürünü sepete ekle
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['urun_id'])) {
    $id   = (int)$_POST['urun_id'];
    $adet = max(1, (int)($_POST['adet'] ?? 1));
    // Ürün bilgisi
    $u = array_values(array_filter($urunler, fn($x)=>$x['id']===$id))[0] ?? null;
    if ($u){
        if (!isset($_SESSION['sepet'][$id])){
            $_SESSION['sepet'][$id] = ['ad'=>$u['ad'],'fiyat'=>$u['fiyat'],'adet'=>0,'img'=>$u['img']];
        }
        $_SESSION['sepet'][$id]['adet'] += $adet;
    }
    // PRG: yenilemede tekrar eklemesin
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Sepette toplam adet
$sepet_adet = array_sum(array_map(fn($x)=>$x['adet'], $_SESSION['sepet']));
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Haklo – Ürünler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ÜST NAV -->
<header class="site-header">
    <div class="container nav">
        <a class="brand" href="#">
            <span class="hlogo">H</span> HAKLO
        </a>

        <form class="search" role="search" onsubmit="event.preventDefault();">
            <input type="search" placeholder="Ara" aria-label="Ara">
        </form>

        <div class="mini-cart" id="miniCart">
            <button class="mini-cart__button" type="button" aria-expanded="false">
                🛒 Sepet <span class="mini-cart__count"><?= (int)$sepet_adet ?></span>
            </button>
            <!-- mini sepet açılır -->
            <div class="mini-cart__panel" hidden>
                <div class="mini-cart__head">Sepetiniz</div>
                <?php if(empty($_SESSION['sepet'])): ?>
                    <div class="mini-cart__empty">Sepetiniz boş.</div>
                <?php else: ?>
                    <ul class="mini-cart__list">
                        <?php foreach($_SESSION['sepet'] as $pid=>$it): ?>
                            <li>
                                <img src="<?= htmlspecialchars($it['img']) ?>" alt="" loading="lazy">
                                <div>
                                    <div class="t1"><?= htmlspecialchars($it['ad']) ?></div>
                                    <div class="t2"><?= (int)$it['adet'] ?> adet • ₺<?= number_format($it['fiyat'],2,',','.') ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a class="btn btn-block" href="#" onclick="alert('Demo: sepete git sayfası yok');return false;">Siparişi Tamamla</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- HERO / DASHBOARD SLIDER -->
<section class="container hero" id="hero">
    <img class="hero__slide is-active" src="img/hero-1.jpg" alt="Ücretsiz kargo ve 30 gün iade">
    <img class="hero__slide" src="img/hero-2.jpg" alt="Yeni sezon">
    <img class="hero__slide" src="img/hero-3.jpg" alt="Spor koleksiyonu">
    <img class="hero__slide" src="img/hero-4.jpg" alt="Aksesuar trendleri">

    <div class="hero__text">
        <div class="kargo">Ücretsiz kargo* ve 30 gün iade</div>
        <div class="alt">En iyi şeyler ücretsizdir*, kargo dahil</div>
    </div>

    <div class="hero__controls" id="heroDots" aria-label="Slider noktaları">
        <button class="dot is-active" aria-label="1. görsel"></button>
        <button class="dot" aria-label="2. görsel"></button>
        <button class="dot" aria-label="3. görsel"></button>
        <button class="dot" aria-label="4. görsel"></button>
    </div>
</section>

<!-- ÜRÜN LİSTESİ -->
<main class="container">
    <h2 class="section-title">Ürünler</h2>
    <div class="grid">
        <?php foreach($urunler as $u): ?>
            <div class="card">
                <img src="<?= htmlspecialchars($u['img']) ?>" alt="<?= htmlspecialchars($u['ad']) ?>" loading="lazy">
                <h3><?= htmlspecialchars($u['ad']) ?></h3>
                <div class="price">₺<?= number_format($u['fiyat'],2,',','.') ?></div>
                <form method="post" class="add-form">
                    <input type="hidden" name="urun_id" value="<?= (int)$u['id'] ?>">
                    <input type="number" name="adet" min="1" value="1" class="qty" aria-label="Adet">
                    <button class="btn" type="submit">Sepete Ekle</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<footer class="container footer">
    <div>Daha fazla marka</div>
</footer>

<!-- JS: Hero slider + mini sepet aç/kapa -->
<script>
    // --- Slider ---
    (()=>{const slides=[...document.querySelectorAll('#hero .hero__slide')],
        dots=[...document.querySelectorAll('#heroDots .dot')];
        let i=0,t;
        const show=n=>{slides[i].classList.remove('is-active');dots[i].classList.remove('is-active');
            i=(n+slides.length)%slides.length;slides[i].classList.add('is-active');dots[i].classList.add('is-active');};
        const play=()=>t=setInterval(()=>show(i+1),4000), stop=()=>clearInterval(t);
        dots.forEach((d,idx)=>d.addEventListener('click',()=>{stop();show(idx);play()}));
        const hero=document.getElementById('hero'); hero.addEventListener('mouseenter',stop); hero.addEventListener('mouseleave',play);
        play();})();

    // --- Mini sepet ---
    (()=>{const wrap=document.getElementById('miniCart');
        const btn=wrap.querySelector('.mini-cart__button');
        const panel=wrap.querySelector('.mini-cart__panel');
        btn.addEventListener('click',()=>{
            const open=panel.hasAttribute('hidden');
            panel.toggleAttribute('hidden', !open);
            btn.setAttribute('aria-expanded', open ? 'true':'false');
        });
        document.addEventListener('click',e=>{
            if(!wrap.contains(e.target)) panel.setAttribute('hidden','');
        });
    })();
</script>

</body>
</html>
