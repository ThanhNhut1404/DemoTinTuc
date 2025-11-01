<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ - Website Tin Tức</title>
    <style>
        body { font-family: Arial; margin: 20px; background-color: #f7f7f7; }
        h1, h2 { color: #006699; }
        .tin, .qc { margin-bottom: 20px; }
        img { max-width: 200px; border-radius: 6px; }
        .slide { background-color: #fff; padding: 10px; border-radius: 10px; }
    </style>
</head>
<body>
    <h1>Trang chủ</h1>

    <div class="slide">
        <h2>Top 5 tin nổi bật</h2>
        <?php foreach ($tinNoiBat as $tin): ?>
            <div class="tin">
                <img src="<?= $tin['anh_dai_dien'] ?>" alt="">
                <p><b><?= $tin['tieu_de'] ?></b></p>
                <small>Ngày đăng: <?= $tin['ngay_dang'] ?> | Lượt xem: <?= $tin['luot_xem'] ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Tin mới nhất</h2>
    <?php foreach ($tinMoiNhat as $tin): ?>
        <div class="tin">
            <b><?= $tin['tieu_de'] ?></b> - <?= $tin['ngay_dang'] ?>
        </div>
    <?php endforeach; ?>

    <h2>Tin xem nhiều</h2>
    <?php foreach ($tinXemNhieu as $tin): ?>
        <div class="tin">
            <?= $tin['tieu_de'] ?> (<?= $tin['luot_xem'] ?> lượt xem)
        </div>
    <?php endforeach; ?>

    <h2>Banner quảng cáo</h2>
    <?php foreach ($quangCao as $qc): ?>
        <div class="qc">
            <a href="<?= $qc['lien_ket'] ?>"><img src="<?= $qc['hinh_anh'] ?>" alt="<?= $qc['tieu_de'] ?>"></a>
        </div>
    <?php endforeach; ?>
</body>
</html>
