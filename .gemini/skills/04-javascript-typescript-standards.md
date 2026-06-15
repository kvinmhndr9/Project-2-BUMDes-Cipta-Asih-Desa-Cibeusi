# JavaScript & TypeScript Standards

> Skill untuk JS/TS di semua environment: browser (vanilla, jQuery), Node.js, React, Next.js, React Native.

## Prinsip Umum

### Event Listener
- **Jangan duplikasi listener.** Jika function bisa dipanggil ulang (SPA, Turbo, hot reload), gunakan guard:
```js
// ✅ Guard pattern
function init() {
    var el = document.getElementById('target');
    if (!el || el.dataset.ready) return;
    el.dataset.ready = '1';
    el.addEventListener('click', handler);
}
```
- Atau gunakan **delegated event** pada parent yang stabil:
```js
// ✅ Delegated — satu listener, banyak elemen
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-action')) { /* handle */ }
});
```

### Variable & Scope
- Gunakan `const` untuk nilai yang tidak berubah, `let` untuk yang berubah.
- Hindari `var` kecuali harus support browser sangat lama.
- Deklarasikan variabel sebelum digunakan — jangan andalkan hoisting.

### Error Handling
```js
// ✅ Try-catch dengan feedback ke user
try {
    const res = await fetch(url);
    if (!res.ok) throw new Error('Request gagal: ' + res.status);
    const data = await res.json();
} catch (err) {
    console.error('Error:', err);
    showAlert('Terjadi kesalahan. Coba lagi.');
}
```

### Async/Await vs Promise
- Prefer `async/await` untuk readability.
- Gunakan `Promise.all()` untuk request paralel.
- Selalu handle rejection (`.catch()` atau `try/catch`).

## DOM Manipulation (Vanilla JS)

### Seleksi Elemen
```js
// ✅ Preferred
document.getElementById('id');           // satu elemen by ID
document.querySelector('.class');        // satu elemen by selector
document.querySelectorAll('.class');     // semua elemen

// ❌ Avoid
document.getElementsByClassName('x');    // live collection, sering menyebabkan bug
```

### Update DOM
- Batch DOM update — jangan baca-tulis-baca-tulis bergantian (menyebabkan layout thrashing).
- Gunakan `textContent` untuk teks (aman dari XSS), `innerHTML` hanya jika perlu HTML.
- Tambahkan `requestAnimationFrame()` untuk animasi DOM.

## React / Next.js

### Komponen
- **Functional component** + hooks (bukan class component).
- Satu komponen per file. Nama file = nama komponen (PascalCase).
- Props destructuring di parameter function.
- Custom hooks untuk logic reusable (prefix `use`).

### State Management
```jsx
// ✅ Local state untuk UI state
const [isOpen, setIsOpen] = useState(false);

// ✅ useEffect dengan cleanup
useEffect(() => {
    const handler = () => { /* ... */ };
    window.addEventListener('resize', handler);
    return () => window.removeEventListener('resize', handler);
}, []);

// ❌ Jangan set state di render tanpa kondisi (infinite loop)
```

### Next.js Spesifik
- Gunakan `app/` router (Next 13+) kecuali project pakai `pages/`.
- Server Components by default, `'use client'` hanya jika butuh interactivity.
- API routes di `app/api/` atau `pages/api/`.
- Image: gunakan `next/image` (optimized).
- Link: gunakan `next/link` (client-side navigation).

## React Native / Mobile

### Layout
- Gunakan `StyleSheet.create()` — bukan inline styles.
- Flexbox adalah default layout di React Native.
- `SafeAreaView` untuk iOS notch.
- `KeyboardAvoidingView` untuk form.

### Performance
- Gunakan `FlatList` / `SectionList` (bukan `ScrollView` + `.map()`) untuk list panjang.
- `React.memo()` untuk komponen yang sering re-render tanpa prop berubah.
- Image: cache dan resize — gunakan library seperti `react-native-fast-image`.

### Navigation
- React Navigation: definisikan semua screen di root navigator.
- Type-safe navigation params jika pakai TypeScript.

## Node.js / Backend JS

### API Response Format (konsisten)
```js
// Success
{ success: true, data: { ... }, message: "OK" }

// Error
{ success: false, error: "Pesan error", code: "VALIDATION_ERROR" }
```

### Environment Variables
- Selalu gunakan `process.env.VAR_NAME` via `.env`.
- Jangan hardcode API key, secret, atau URL production.
- Validasi env vars saat startup (fail fast).

## Anti-Pattern JS

1. ❌ `eval()` — security risk
2. ❌ `document.write()` — menghapus seluruh DOM
3. ❌ Callback hell — gunakan async/await
4. ❌ Memory leak dari event listener yang tidak di-remove
5. ❌ `setTimeout(init, 0)` sebagai "fix" tanpa memahami root cause
6. ❌ Mutasi state langsung (array.push di React state) — selalu buat copy baru
