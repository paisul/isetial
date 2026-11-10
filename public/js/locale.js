(() => {
    'use strict';

    const storageKey = 'isetial_locale';
    const dictionary = window.isetialTranslations?.th || {};

    const patterns = [
        [/^Petugas (.+)$/u, 'เจ้าหน้าที่ $1'], [/^Periode (.+)$/u, 'วาระ $1'],
        [/^Pilih (.+)$/u, 'เลือก$1'],
        [/^Lengan pendek · Ukuran (.+)$/u, 'แขนสั้น · ขนาด $1'],
        [/^Lengan panjang · Ukuran (.+)$/u, 'แขนยาว · ขนาด $1'],
        [/^0 tersedia$/u, 'สินค้าหมด'],         [/^(\d+) tersedia$/u, 'มี $1 ชิ้น'], [/^(\d+) tersisa$/u, 'เหลือ $1 ชิ้น'],
        [/^© (\d{4}) iSetial Wisdom · Pemuda-Pemudi 5 Masjid$/u, '© $1 iSetial Wisdom · เยาวชนจาก 5 มัสยิด'],
        [/^(.+) · Lelaki Pendek · Pendek · (.+)$/u, '$1 · ผู้ชายแขนสั้น · แขนสั้น · $2'],
        [/^(.+) · Lelaki Panjang · Panjang · (.+)$/u, '$1 · ผู้ชายแขนยาว · แขนยาว · $2'],
        [/^(.+) · Muslimah · Panjang · (.+)$/u, '$1 · มุสลิมะฮ์ · แขนยาว · $2'],
        [/^(.+) · Lelaki Pendek · Lengan pendek · Ukuran (.+)$/u, '$1 · ผู้ชายแขนสั้น · แขนสั้น · ขนาด $2'],
        [/^(.+) · Lelaki Panjang · Lengan panjang · Ukuran (.+)$/u, '$1 · ผู้ชายแขนยาว · แขนยาว · ขนาด $2'],
        [/^(.+) · Muslimah · Lengan panjang · Ukuran (.+)$/u, '$1 · มุสลิมะฮ์ · แขนยาว · ขนาด $2'],
        [/^(\d+) jersey$/u, '$1 ตัว'], [/^Total (\d+) jersey$/u, 'รวม $1 ตัว'], [/^(\d+) baju$/u, '$1 ตัว'], [/^(\d+) buah$/u, '$1 ชิ้น'],
        [/^Ukuran (.+)$/u, 'ขนาด $1'], [/^Sebutkan nomor pesanan (.+) kepada petugas atau admin\.$/u, 'แจ้งเลขที่คำสั่งซื้อ $1 แก่เจ้าหน้าที่หรือผู้ดูแลระบบ'],
        [/^(.+) · Lengan pendek · Ukuran (.+)$/u, '$1 · แขนสั้น · ขนาด $2'],
        [/^(.+) · Lengan panjang · Ukuran (.+)$/u, '$1 · แขนยาว · ขนาด $2'],
        [/^(.+) · Lengan pendek$/u, '$1 · แขนสั้น'], [/^(.+) · Lengan panjang$/u, '$1 · แขนยาว'],
        [/^(.+) · Ukuran (.+) · (\d+) buah$/u, '$1 · ขนาด $2 · $3 ชิ้น'],
        [/^Buka keranjang, (\d+) item$/u, 'เปิดตะกร้า, $1 รายการ'],
        [/^Petugas \((\d+)\/10\)$/u, 'เจ้าหน้าที่ ($1/10)'],
        [/^Menampilkan (\d+) pesanan$/u, 'แสดง $1 คำสั่งซื้อ'],
        [/^(\d+) bukti baru$/u, 'หลักฐานใหม่ $1 รายการ'],
        [/^Petugas: (.+)$/u, 'เจ้าหน้าที่: $1'],
        [/^Rincian pesanan \((\d+) jersey\)$/u, 'รายละเอียดคำสั่งซื้อ ($1 ตัว)'],
        [/^(.+) · (\d+) pesanan$/u, '$1 · $2 คำสั่งซื้อ'],
        [/^Status produksi (.+)$/u, 'สถานะการผลิต $1'],
        [/^Hapus pesanan (.+)\? Data akan diarsipkan dan tidak tampil lagi\.$/u, 'ลบคำสั่งซื้อ $1 หรือไม่? ข้อมูลจะถูกเก็บถาวรและไม่แสดงอีก'],
        [/^(\d{1,2}) (Jan|Feb|Mar|Apr|Mei|Jun|Jul|Agu|Sep|Okt|Nov|Des) (\d{4})(.*)$/u, (all, day, month, year, rest) => `${day} ${{Jan:'ม.ค.',Feb:'ก.พ.',Mar:'มี.ค.',Apr:'เม.ย.',Mei:'พ.ค.',Jun:'มิ.ย.',Jul:'ก.ค.',Agu:'ส.ค.',Sep:'ก.ย.',Okt:'ต.ค.',Nov:'พ.ย.',Des:'ธ.ค.'}[month]} ${Number(year) + 543}${rest}`]
    ];
    const originalText = new WeakMap();
    const originalAttributes = new WeakMap();
    const originalTitle = document.title;
    let locale = localStorage.getItem(storageKey) === 'th' ? 'th' : 'id';
    let applying = false;

    const translate = value => {
        const trimmed = value.trim();
        if (!trimmed) return value;
        let result = dictionary[trimmed];
        if (!result) {
            for (const [pattern, replacement] of patterns) {
                if (pattern.test(trimmed)) { result = trimmed.replace(pattern, replacement); break; }
            }
        }
        return result ? value.replace(trimmed, result) : value;
    };

    const blocked = node => node.parentElement?.closest('script, style, noscript, [data-no-translate]');

    function applyText(node, refresh = false) {
        if (blocked(node)) return;
        if (refresh && originalText.has(node)) {
            const expected = locale === 'th' ? translate(originalText.get(node)) : originalText.get(node);
            if (node.nodeValue === expected) return;
        }
        if (refresh || !originalText.has(node)) originalText.set(node, node.nodeValue);
        const value = locale === 'th' ? translate(originalText.get(node)) : originalText.get(node);
        if (node.nodeValue !== value) node.nodeValue = value;
    }

    function applyElement(element, refresh = false) {
        if (!(element instanceof Element) || element.closest('[data-no-translate]')) return;
        const walker = document.createTreeWalker(element, NodeFilter.SHOW_TEXT);
        while (walker.nextNode()) applyText(walker.currentNode, refresh);
        const targets = [element, ...element.querySelectorAll('[placeholder], [aria-label], [title], [alt]')];
        targets.forEach(target => {
            const saved = originalAttributes.get(target) || {};
            ['placeholder', 'aria-label', 'title', 'alt'].forEach(attribute => {
                if (!target.hasAttribute(attribute)) return;
                if (refresh || saved[attribute] === undefined) saved[attribute] = target.getAttribute(attribute);
                const value = locale === 'th' ? translate(saved[attribute]) : saved[attribute];
                if (target.getAttribute(attribute) !== value) target.setAttribute(attribute, value);
            });
            originalAttributes.set(target, saved);
        });
    }

    function updateButtons() {
        document.querySelectorAll('[data-locale]').forEach(button => {
            const active = button.dataset.locale === locale;
            button.classList.toggle('bg-amber-400', active);
            button.classList.toggle('text-emerald-950', active);
            button.classList.toggle('text-emerald-100', !active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
    }

    function applyPage() {
        applying = true;
        document.documentElement.lang = locale === 'th' ? 'th' : 'id';
        document.title = locale === 'th' ? translate(originalTitle) : originalTitle;
        applyElement(document.body);
        updateButtons();
        applying = false;
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-locale]').forEach(button => button.addEventListener('click', () => {
            locale = button.dataset.locale === 'th' ? 'th' : 'id';
            localStorage.setItem(storageKey, locale);
            applyPage();
        }));
        applyPage();
        new MutationObserver(mutations => {
            if (applying) return;
            applying = true;
            mutations.forEach(mutation => {
                if (mutation.type === 'characterData') applyText(mutation.target, true);
                mutation.addedNodes.forEach(node => node.nodeType === Node.TEXT_NODE ? applyText(node) : applyElement(node));
            });
            applying = false;
        }).observe(document.body, {childList: true, characterData: true, subtree: true});
    });

    window.isetialTranslate = text => locale === 'th' ? translate(text) : text;
    const nativeConfirm = window.confirm.bind(window);
    window.confirm = message => nativeConfirm(window.isetialTranslate(String(message)));
})();
