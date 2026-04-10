import './bootstrap';

// Turbo Drive - SPA navigation, giữ nguyên các element có data-turbo-permanent
import * as Turbo from '@hotwired/turbo';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);

window.Alpine = Alpine;

Alpine.start();

// Mô phỏng DOMContentLoaded cho các inline script cũ sau Turbo navigate
document.addEventListener('turbo:load', () => {
    window.dispatchEvent(new Event('DOMContentLoaded'));
});

/**
 * Xử lý Alpine.js + Turbo Drive lifecycle cho permanent elements.
 * 
 * Vấn đề: Khi Turbo navigate, nó tháo permanent elements ra khỏi DOM cũ
 * rồi gắn vào DOM mới. Alpine's MutationObserver phát hiện → destroyTree()
 * → mất tất cả event listeners và $watch bindings.
 * 
 * Giải pháp: Sau khi Turbo render xong, force destroy + re-init Alpine
 * trên permanent elements. Singleton pattern đảm bảo state được giữ nguyên.
 */
document.addEventListener('turbo:render', () => {
    // Re-init Alpine trên tất cả x-data components trong permanent zones
    document.querySelectorAll('[data-turbo-permanent] [x-data]').forEach(el => {
        // Destroy tree cũ (nếu còn) để dọn sạch bindings cũ
        if (el._x_dataStack) {
            Alpine.destroyTree(el);
        }
        // Init tree mới - Alpine sẽ gọi x-data function (trả về singleton state)
        // rồi gọi init() (sẽ chỉ re-register watchers, không tạo lại Audio/YT)
        Alpine.initTree(el);
    });

    // Cũng init cho chính element permanent nếu có x-data
    document.querySelectorAll('[data-turbo-permanent][x-data]').forEach(el => {
        if (el._x_dataStack) {
            Alpine.destroyTree(el);
        }
        Alpine.initTree(el);
    });
});
