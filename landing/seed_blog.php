<?php
require_once __DIR__ . '/includes/db.php';
header('Content-Type: text/plain; charset=utf-8');

try {
    $pdo = master_pdo();
    $count = (int)$pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
    if ($count > 0) {
        echo "SKIP: đã có $count bài, không seed.\n";
        @unlink(__FILE__);
        exit;
    }

    $posts = [
        [
            'slug' => 'top-phan-mem-quan-ly-ban-hang-cua-hang-nho',
            'title' => 'Top 5 Phần Mềm Quản Lý Bán Hàng Cho Cửa Hàng Nhỏ Năm 2026 (So Sánh Giá)',
            'excerpt' => 'So sánh chi tiết 5 phần mềm quản lý bán hàng phổ biến nhất cho chủ shop nhỏ ở Việt Nam: giá, tính năng, ưu nhược điểm — cập nhật 2026.',
            'meta_description' => 'So sánh giá và tính năng 5 phần mềm quản lý bán hàng tốt nhất cho cửa hàng nhỏ 2026: KiotViet, Sapo, Haravan, MISA, Quản Lý Bán Hàng.',
            'cover_image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1200&q=80',
            'tags' => 'phan-mem-quan-ly-ban-hang,cua-hang-nho,so-sanh-gia',
            'content' => <<<'HTML'
<p class="lead">Bạn đang mở cửa hàng nhỏ và phân vân không biết chọn <strong>phần mềm quản lý bán hàng</strong> nào? Bài viết này sẽ so sánh 5 lựa chọn phổ biến nhất tại Việt Nam năm 2026 — từ giá cả, tính năng tới đối tượng phù hợp — để bạn quyết định nhanh và đúng.</p>

<h2>1. KiotViet</h2>
<p>KiotViet là một trong những phần mềm quản lý bán hàng phổ biến nhất Việt Nam, định vị ở phân khúc trung cấp đến cao cấp.</p>
<ul>
  <li><strong>Giá:</strong> từ ~180.000đ/tháng/cửa hàng (gói cơ bản), gói chuyên nghiệp 270.000đ trở lên.</li>
  <li><strong>Ưu điểm:</strong> Giao diện quen thuộc, hỗ trợ đa ngành, có app mobile, kho ứng dụng mở rộng.</li>
  <li><strong>Nhược điểm:</strong> Càng nhiều chi nhánh giá càng đội lên, một số tính năng nâng cao chỉ có ở gói cao.</li>
</ul>

<h2>2. Sapo POS</h2>
<p>Sapo nổi tiếng với hệ sinh thái bán hàng online + offline.</p>
<ul>
  <li><strong>Giá:</strong> ~199.000đ/tháng cho gói cơ bản POS.</li>
  <li><strong>Ưu điểm:</strong> Tích hợp tốt với sàn TMĐT, vận chuyển, quản lý đơn hàng đa kênh.</li>
  <li><strong>Nhược điểm:</strong> Quá nhiều tính năng nếu bạn chỉ có 1 cửa hàng nhỏ, dễ rối.</li>
</ul>

<h2>3. Haravan</h2>
<p>Haravan thiên về thương mại điện tử nhưng cũng có giải pháp POS.</p>
<ul>
  <li><strong>Giá:</strong> POS từ 200.000đ/tháng trở lên.</li>
  <li><strong>Ưu điểm:</strong> Mạnh ở phần website bán hàng, đồng bộ tồn kho online-offline.</li>
  <li><strong>Nhược điểm:</strong> Đắt với shop chỉ bán offline thuần tuý.</li>
</ul>

<h2>4. MISA eShop / MISA CukCuk</h2>
<p>Thương hiệu MISA quen thuộc trong mảng kế toán, cũng có giải pháp bán hàng riêng.</p>
<ul>
  <li><strong>Giá:</strong> ~180.000 – 300.000đ/tháng tuỳ gói.</li>
  <li><strong>Ưu điểm:</strong> Đồng bộ tốt với kế toán MISA, phù hợp cửa hàng cần xuất hoá đơn.</li>
  <li><strong>Nhược điểm:</strong> Giao diện thiên về kế toán, có thể hơi khô khan với chủ shop.</li>
</ul>

<h2>5. Quản Lý Bán Hàng (quanlybanhang.shop)</h2>
<p>Đây là giải pháp mới hơn, định vị rõ ràng: <strong>RẺ + GỌN NHẸ</strong> cho chủ shop, chủ cửa hàng bán lẻ Việt Nam.</p>
<ul>
  <li><strong>Giá:</strong> Dùng thử 7 ngày miễn phí, bảng giá công khai tại <a href="/landing/pricing.php">/pricing</a>.</li>
  <li><strong>Ưu điểm:</strong> Đăng ký 1 phút có subdomain riêng, quản lý tồn kho rõ ràng, in bill K80 sẵn, xem doanh số theo chi nhánh, hỗ trợ tiếng Việt.</li>
  <li><strong>Nhược điểm:</strong> Hệ sinh thái app mở rộng chưa nhiều bằng KiotViet.</li>
</ul>

<blockquote>👉 Phù hợp: chủ shop quần áo, tạp hoá, mỹ phẩm, quán cà phê nhỏ — muốn phần mềm gọn, đủ dùng, giá hợp lý, không cần học cả tuần.</blockquote>

<h2>Bảng so sánh nhanh</h2>
<table>
<thead><tr><th>Phần mềm</th><th>Giá khởi điểm</th><th>Phù hợp</th></tr></thead>
<tbody>
<tr><td>KiotViet</td><td>~180k/tháng</td><td>Shop quy mô vừa, đa ngành</td></tr>
<tr><td>Sapo</td><td>~199k/tháng</td><td>Bán đa kênh online + offline</td></tr>
<tr><td>Haravan</td><td>~200k/tháng</td><td>Cửa hàng có website</td></tr>
<tr><td>MISA</td><td>~180k/tháng</td><td>Cần xuất hoá đơn, đồng bộ kế toán</td></tr>
<tr><td>Quản Lý Bán Hàng</td><td>Thử miễn phí 7 ngày</td><td>Shop nhỏ, cần rẻ + gọn nhẹ</td></tr>
</tbody>
</table>

<h3>Kết luận</h3>
<p>Không có phần mềm "tốt nhất" cho mọi người. Chọn theo nhu cầu thực tế:</p>
<ul>
  <li>Shop nhỏ, ưu tiên rẻ và đơn giản → <strong>Quản Lý Bán Hàng</strong>.</li>
  <li>Bán đa kênh online-offline → Sapo / Haravan.</li>
  <li>Cần kế toán + hoá đơn → MISA.</li>
  <li>Cần hệ sinh thái lớn, nhiều add-on → KiotViet.</li>
</ul>
<p><a href="/landing/register.php">👉 Dùng thử miễn phí Quản Lý Bán Hàng 7 ngày</a> để cảm nhận tận tay trước khi quyết định.</p>
HTML
        ],
        [
            'slug' => 'cach-quan-ly-ton-kho-cua-hang-ban-le',
            'title' => 'Cách Quản Lý Tồn Kho Hiệu Quả Cho Chủ Cửa Hàng Bán Lẻ: Hướng Dẫn Từ A-Z',
            'excerpt' => 'Hướng dẫn chi tiết 7 mẹo quản lý tồn kho cho chủ shop bán lẻ — từ kiểm kê định kỳ, safety stock đến ABC analysis và dùng phần mềm thay sổ tay.',
            'meta_description' => 'Hướng dẫn quản lý tồn kho từ A-Z cho chủ cửa hàng bán lẻ: 7 mẹo thực chiến giúp giảm thất thoát, tối ưu vốn, không lo cháy hàng.',
            'cover_image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1200&q=80',
            'tags' => 'quan-ly-ton-kho,huong-dan,ban-le',
            'content' => <<<'HTML'
<p class="lead">Tồn kho là "tiền chết" — quản không khéo, bạn vừa bị chôn vốn vừa bị thất thoát mà không biết. Bài này tổng hợp 7 mẹo thực chiến đã được nhiều chủ shop áp dụng thành công.</p>

<h2>1. Kiểm kê định kỳ (cycle counting)</h2>
<p>Đừng đợi cuối năm mới kiểm kê. Hãy chia kho thành 4 vùng và mỗi tuần kiểm 1 vùng. Cuối tháng bạn đã kiểm hết kho mà không cần đóng cửa.</p>
<blockquote>💡 Mẹo: ưu tiên kiểm các mặt hàng giá trị cao trước.</blockquote>

<h2>2. Đặt safety stock (tồn kho an toàn)</h2>
<p>Mỗi SKU nên có mức tồn kho tối thiểu. Khi xuống dưới mức này, hệ thống cảnh báo bạn nhập thêm.</p>
<ul>
  <li>Công thức đơn giản: <em>Safety stock = (số bán trung bình/ngày) × (số ngày từ lúc đặt đến lúc nhận hàng) × 1.5</em></li>
</ul>

<h2>3. Phân tích ABC</h2>
<p>Chia hàng thành 3 nhóm:</p>
<ul>
  <li><strong>A:</strong> 20% mặt hàng tạo 80% doanh thu → ưu tiên kiểm kê tuần, không bao giờ hết hàng.</li>
  <li><strong>B:</strong> 30% mặt hàng tạo 15% doanh thu.</li>
  <li><strong>C:</strong> 50% còn lại tạo 5% doanh thu → chỉ kiểm kê tháng/quý.</li>
</ul>

<h2>4. Dùng phần mềm thay sổ tay</h2>
<p>Sổ tay và Excel chỉ đủ cho 1 cửa hàng dưới 100 SKU. Quá mức đó là nguy hiểm.</p>
<p>Phần mềm như <a href="/">Quản Lý Bán Hàng</a> cho phép bạn xem tồn kho realtime trên điện thoại, cảnh báo khi gần hết, tự động trừ kho khi bán — không còn cảnh "khách hỏi mới biết hết hàng".</p>

<h2>5. In mã vạch cho mọi SKU</h2>
<p>Mã vạch giúp giảm sai sót khi nhập/xuất kho, tăng tốc thanh toán, và tạo cảm giác chuyên nghiệp.</p>

<h2>6. Theo dõi sản phẩm chậm bán</h2>
<p>Sản phẩm tồn quá 90 ngày là tín hiệu cần hành động:</p>
<ul>
  <li>Giảm giá xả hàng.</li>
  <li>Bundle kèm sản phẩm bán chạy.</li>
  <li>Trả lại nhà cung cấp (nếu hợp đồng cho phép).</li>
</ul>

<h2>7. Phân tích vòng quay hàng tồn kho</h2>
<p>Công thức: <em>Vòng quay = Giá vốn hàng bán / Tồn kho trung bình</em>. Vòng quay cao = quản kho khoẻ. Bán lẻ trung bình nên đạt 6-12 vòng/năm.</p>

<blockquote>🛠 Công cụ gợi ý: <strong>Quản Lý Bán Hàng</strong> có sẵn báo cáo tồn kho theo chi nhánh, cảnh báo dưới mức tối thiểu, lịch sử nhập xuất rõ ràng. <a href="/landing/register.php">Dùng thử miễn phí 7 ngày</a>.</blockquote>

<h3>Tổng kết</h3>
<p>Quản tốt tồn kho = giảm vốn chết + giảm thất thoát + tăng doanh thu. Bắt đầu từ những việc nhỏ: kiểm kê tuần, set safety stock, dùng phần mềm thay sổ tay. Sau 1 tháng bạn sẽ thấy khác biệt.</p>
HTML
        ],
        [
            'slug' => 'phan-mem-in-bill-k80-may-in-nhiet',
            'title' => '7 Lý Do Cửa Hàng Của Bạn Nên Có Phần Mềm In Bill Khổ K80 (Máy In Nhiệt)',
            'excerpt' => 'Tại sao máy in nhiệt khổ K80 đang trở thành chuẩn cho cửa hàng bán lẻ Việt Nam? 7 lý do thuyết phục và cách chọn phần mềm tương thích.',
            'meta_description' => 'Máy in nhiệt khổ K80 giúp tiết kiệm 70% chi phí giấy in, tăng tốc thanh toán và chuyên nghiệp hoá cửa hàng. 7 lý do bạn nên đổi ngay.',
            'cover_image' => 'https://images.unsplash.com/photo-1556740758-90de374c12ad?w=1200&q=80',
            'tags' => 'in-bill,may-in-nhiet,k80',
            'content' => <<<'HTML'
<p class="lead">Bạn vẫn dùng giấy A4 cắt đôi để in hoá đơn? Đã đến lúc nâng cấp lên máy in nhiệt khổ K80 — đây là chuẩn mới của bán lẻ Việt Nam và lý do thì rất rõ.</p>

<h2>K80 là gì?</h2>
<p>K80 là khổ giấy in nhiệt rộng 80mm (tương ứng máy in nhiệt 80mm). Đây là khổ tiêu chuẩn cho hoá đơn POS, được dùng trong hầu hết siêu thị, quán cà phê, nhà hàng và cửa hàng bán lẻ hiện đại.</p>

<h2>1. Tiết kiệm chi phí giấy in</h2>
<p>1 cuộn K80 (~60.000đ) in được 500-700 bill. Tính ra mỗi bill chỉ ~100đ. So với giấy A4 + mực thì rẻ hơn 3-5 lần.</p>

<h2>2. Không cần mực</h2>
<p>Máy in nhiệt dùng đầu in nhiệt làm đen giấy — không có ống mực để hết. Không có cảnh giữa giờ cao điểm máy báo "hết mực".</p>

<h2>3. In siêu nhanh</h2>
<p>Một bill khoảng 1-2 giây. Khách không phải đứng chờ. Trải nghiệm thanh toán mượt hơn nhiều so với in A4.</p>

<h2>4. Trông chuyên nghiệp</h2>
<p>Bill khổ K80 là chuẩn mà khách quen mắt khi mua ở siêu thị, Highlands, KFC… Cửa hàng nhỏ dùng K80 ngay lập tức trông "đầu tư" hơn.</p>

<h2>5. Gọn, không chiếm chỗ</h2>
<p>Máy in nhiệt nhỏ bằng nửa bàn tay, gắn cạnh máy tính bán hàng hoặc điện thoại đều được. Không chiếm bàn thu ngân.</p>

<h2>6. Tương thích đa thiết bị</h2>
<p>Hầu hết máy in K80 hiện nay hỗ trợ cả USB, Bluetooth, LAN. Bạn có thể in từ máy tính, điện thoại, máy POS đều ổn.</p>

<h2>7. Phần mềm bán hàng tốt đều hỗ trợ sẵn</h2>
<p>Đây là điểm quan trọng nhất. <a href="/">Phần mềm Quản Lý Bán Hàng</a> đã hỗ trợ in K80 sẵn — bạn không cần config phức tạp:</p>
<ul>
  <li>Cắm máy in vào.</li>
  <li>Mở phần mềm, bấm "In bill".</li>
  <li>Xong. Không cần driver lằng nhằng, không cần plugin trình duyệt.</li>
</ul>

<blockquote>💡 Mẹo chọn máy in: ưu tiên máy có tốc độ 200mm/s, hỗ trợ USB + Bluetooth, giá tầm 700k-1.2 triệu là đủ dùng. Tránh máy không thương hiệu giá <500k vì dễ kẹt giấy.</blockquote>

<h3>Kết luận</h3>
<p>Nếu cửa hàng của bạn vẫn đang in bill bằng A4 hay viết tay, đầu tư 1 triệu cho máy in K80 + 1 phần mềm POS gọn nhẹ là quyết định bạn sẽ không hối tiếc. Nó vừa tiết kiệm chi phí lâu dài, vừa nâng cấp trải nghiệm khách hàng.</p>

<p><a href="/landing/register.php">👉 Dùng thử miễn phí Quản Lý Bán Hàng 7 ngày</a> — đã hỗ trợ in K80 sẵn, không tốn 1 phút cấu hình.</p>
HTML
        ],
    ];

    $stmt = $pdo->prepare("INSERT INTO blog_posts(slug,title,excerpt,cover_image,content,tags,meta_description,status,published_at) VALUES(:s,:t,:e,:c,:co,:tg,:md,'published',NOW())");
    $i = 0;
    foreach ($posts as $p) {
        $stmt->execute([
            ':s' => $p['slug'], ':t' => $p['title'], ':e' => $p['excerpt'],
            ':c' => $p['cover_image'], ':co' => $p['content'], ':tg' => $p['tags'],
            ':md' => $p['meta_description'],
        ]);
        $i++;
    }
    echo "OK: seed $i bài.\n";
    @unlink(__FILE__);
    echo "Self-deleted.\n";
} catch (Exception $e) {
    http_response_code(500);
    echo "ERROR: " . $e->getMessage();
}
