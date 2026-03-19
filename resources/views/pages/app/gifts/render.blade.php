<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $giftPage->meta_title }}</title>
    
    {{-- Open Graph Meta Tags cho Zalo/Facebook preview --}}
    <meta property="og:title" content="{{ $giftPage->meta_title }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    @if($giftPage->meta_image)
        <meta property="og:image" content="{{ $giftPage->meta_image }}">
    @endif
    <meta property="og:description" content="Trang quà tặng tương tác được tạo tại NDHShop">

    <style>
        body, html {
            margin: 0; padding: 0; width: 100%; height: 100%;
            overflow: hidden; background-color: #0b0f17; font-family: sans-serif;
        }

        /* Intro Screen - Bắt buộc click để un-mute audio trên trình duyệt hiện đại */
        #intro-screen {
            position: fixed; inset: 0; z-index: 9999;
            background: linear-gradient(135deg, #0b0f17, #1a2030);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            color: white; cursor: pointer; transition: opacity 0.5s ease;
        }

        #intro-screen h1 { font-size: 2rem; margin-bottom: 1rem; text-align: center; padding: 0 1rem; }
        
        .pulse-btn {
            background: #ff4757; color: white; border: none; padding: 12px 32px;
            font-size: 1.1rem; font-weight: bold; border-radius: 50px;
            box-shadow: 0 0 0 0 rgba(255, 71, 87, 0.7);
            animation: pulse 1.5s infinite; cursor: pointer;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 71, 87, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 15px rgba(255, 71, 87, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 71, 87, 0); }
        }

        /* Iframe chạy template an toàn */
        #gift-frame {
            width: 100%; height: 100%; border: none; display: none;
        }
    </style>
</head>
<body>

    {{-- Màn hình Intro: Yêu cầu click để mở quà --}}
    <div id="intro-screen" onclick="openGift()">
        <h1>Ai đó đã gửi cho bạn một món quà! 🎁</h1>
        <button class="pulse-btn">Nhấn để mở</button>
    </div>

    {{-- Sandbox Iframe chứa mã quà tặng --}}
    {{-- 
        sandbox="allow-scripts": Cho phép JS hiệu ứng chạy.
        sandbox="allow-same-origin": Cần thiết nếu template có load ảnh local tài nguyên CDN.
        KHÔNG cho phép allow-forms, allow-top-navigation, allow-modals để bảo mật tối đa.
    --}}
    <iframe id="gift-frame" sandbox="allow-scripts allow-same-origin" title="Gift Preview"></iframe>

    <script>
        // Inject mã mẫu vào Iframe srcdoc an toàn
        // Ta sử dụng Base64 decode thay vì nối chuỗi trực tiếp để tránh lỗi nháy đơn/nháy kép phá vỡ cấu trúc JS
        const rawContentBase64 = "{{ base64_encode($htmlCode) }}";
        
        function openGift() {
            const intro = document.getElementById('intro-screen');
            const frame = document.getElementById('gift-frame');

            intro.style.opacity = '0';
            
            // Render nội dung thiệp vào iframe
            // Giải mã Base64 sang UTF-8
            const rawContent = decodeURIComponent(escape(window.atob(rawContentBase64)));
            frame.setAttribute('srcdoc', rawContent);
            frame.style.display = 'block';

            setTimeout(() => {
                intro.remove();
            }, 500);
        }
    </script>
</body>
</html>
