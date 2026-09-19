@extends('layouts.app')
@section('title','Pesan Jersey')
@section('content')
@php
$catalog = $products->map(fn ($product) => [
    'id' => $product->id,
    'name' => $product->name,
    'description' => $product->description,
    'price' => (float) $product->price,
    'sizes' => $product->sizes->map(fn ($size) => ['id' => $size->id, 'name' => $size->name, 'adjustment' => (float) $size->price_adjustment])->values(),
])->values();
$oldItems = old('items', []);
@endphp

<header class="relative overflow-hidden bg-emerald-950 text-white">
    <div class="absolute inset-0 opacity-20" style="background-image:radial-gradient(circle at 85% 20%,#fbbf24 0,transparent 24%),radial-gradient(circle at 10% 90%,#34d399 0,transparent 28%)"></div>
    <div class="relative mx-auto max-w-7xl px-4 py-10 md:py-14">
        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2 rounded-full border border-amber-300/30 bg-amber-300/10 px-3 py-1 text-xs font-bold uppercase tracking-[.18em] text-amber-300">Jersey Resmi iSetial Wisdom</span>
            <h1 class="mt-4 text-4xl font-black tracking-tight md:text-5xl">Pesan jersey dengan mudah</h1>
            <p class="mt-3 max-w-2xl text-base leading-7 text-emerald-100 md:text-lg">Pilih beberapa jersey, isi data pemesan sekali, lalu periksa semuanya sebelum dikirim.</p>
        </div>
    </div>
</header>

<div class="mx-auto max-w-7xl px-4 py-8 md:py-10">
@if($products->isEmpty())
    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-8 text-center"><div class="text-4xl">⏳</div><h2 class="mt-3 text-xl font-black">Pemesanan belum dibuka</h2><p class="mt-2 text-slate-600">Silakan hubungi pengurus untuk informasi berikutnya.</p></div>
@else
<form id="jersey-form" method="post" action="{{route('jersey.store')}}" novalidate>@csrf
    <nav aria-label="Tahapan pemesanan" class="mb-8 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
        <ol class="grid grid-cols-3 gap-2">
            @foreach([1=>'Pilih jersey',2=>'Data pemesan',3=>'Periksa pesanan'] as $number=>$label)
            <li data-step-indicator="{{$number}}" class="flex items-center gap-2 rounded-xl px-2 py-2 text-slate-400 md:px-4">
                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full border-2 text-sm font-black">{{$number}}</span>
                <span class="hidden text-sm font-bold sm:block">{{$label}}</span>
            </li>
            @endforeach
        </ol>
    </nav>

    <div class="grid items-start gap-7 lg:grid-cols-[minmax(0,1fr)_340px]">
        <div>
            <section data-step-panel="1">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div><p class="text-xs font-black uppercase tracking-[.16em] text-emerald-700">Langkah 1 dari 3</p><h2 class="mt-1 text-2xl font-black tracking-tight md:text-3xl">Pilih jersey</h2><p class="mt-2 text-slate-500">Tambahkan item jika model atau ukurannya berbeda.</p></div>
                    <button id="add-item" type="button" class="inline-flex items-center gap-2 rounded-xl bg-emerald-800 px-4 py-3 text-sm font-black text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200"><span class="text-xl leading-none">+</span> Tambah jersey</button>
                </div>
                <div id="order-items" class="mt-5 space-y-5"></div>
                <div class="mt-4 flex items-center gap-2 text-xs text-slate-500"><span class="grid h-5 w-5 place-items-center rounded-full bg-slate-200 font-bold">i</span>Maksimal 20 jenis jersey, masing-masing hingga 20 buah.</div>
            </section>

            <section data-step-panel="2" hidden>
                <p class="text-xs font-black uppercase tracking-[.16em] text-emerald-700">Langkah 2 dari 3</p><h2 class="mt-1 text-2xl font-black tracking-tight md:text-3xl">Data pemesan</h2><p class="mt-2 text-slate-500">Data ini digunakan untuk konfirmasi dan mengecek status pesanan.</p>
                <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm md:p-7">
                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="text-sm font-bold text-slate-700">Nama lengkap <span class="text-red-600">*</span><input id="customer-name" name="customer_name" value="{{old('customer_name')}}" required maxlength="150" autocomplete="name" placeholder="Contoh: Ahmad Fauzan" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 font-normal outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"></label>
                        <label class="text-sm font-bold text-slate-700">Nomor HP/WhatsApp <span class="text-red-600">*</span><input id="customer-phone" type="tel" name="phone" value="{{old('phone')}}" required maxlength="30" inputmode="tel" autocomplete="tel" placeholder="Contoh: 0812 3456 7890" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 font-normal outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100"><span class="mt-1.5 block text-xs font-normal text-slate-400">Pastikan nomor aktif dan dapat dihubungi.</span></label>
                        <label class="text-sm font-bold text-slate-700 md:col-span-2">Alamat lengkap <span class="text-red-600">*</span><textarea id="customer-address" name="address" required maxlength="1000" rows="4" autocomplete="street-address" placeholder="Nama jalan, nomor rumah, desa/kecamatan, dan patokan" class="mt-2 w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 font-normal outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100">{{old('address')}}</textarea></label>
                    </div>
                    <div class="mt-5 flex items-start gap-3 rounded-2xl bg-emerald-50 p-4 text-sm text-emerald-900"><span aria-hidden="true">🔒</span><p><b>Data Anda aman.</b> Informasi ini hanya dipakai untuk memproses pesanan jersey.</p></div>
                </div>
            </section>

            <section data-step-panel="3" hidden>
                <p class="text-xs font-black uppercase tracking-[.16em] text-emerald-700">Langkah 3 dari 3</p><h2 class="mt-1 text-2xl font-black tracking-tight md:text-3xl">Periksa pesanan</h2><p class="mt-2 text-slate-500">Pastikan model, ukuran, jumlah, dan data pemesan sudah benar.</p>
                <div class="mt-6 space-y-5">
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm md:p-7"><div class="flex items-center justify-between"><h3 class="text-lg font-black">Jersey yang dipesan</h3><button type="button" data-go-step="1" class="text-sm font-bold text-emerald-700 hover:underline">Ubah</button></div><div id="review-items" class="mt-4 divide-y divide-slate-100"></div></div>
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm md:p-7"><div class="flex items-center justify-between"><h3 class="text-lg font-black">Data pemesan</h3><button type="button" data-go-step="2" class="text-sm font-bold text-emerald-700 hover:underline">Ubah</button></div><dl id="review-customer" class="mt-4 grid gap-4 text-sm md:grid-cols-2"></dl></div>
                    <div class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950"><span aria-hidden="true">💳</span><p>Pembayaran dilakukan setelah nomor pesanan berhasil dibuat. Anda dapat mengunggah bukti pembayaran pada halaman pesanan.</p></div>
                </div>
            </section>

            <div class="mt-7 flex items-center justify-between gap-3 border-t border-slate-200 pt-6">
                <button id="back-button" type="button" class="hidden rounded-xl border border-slate-300 bg-white px-5 py-3 font-bold text-slate-700 transition hover:bg-slate-50">← Kembali</button>
                <button id="next-button" type="button" class="ml-auto rounded-xl bg-emerald-800 px-6 py-3 font-black text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">Lanjutkan →</button>
                <button id="submit-button" type="submit" class="ml-auto hidden rounded-xl bg-amber-400 px-6 py-3 font-black text-emerald-950 shadow-sm transition hover:bg-amber-300 focus:outline-none focus:ring-4 focus:ring-amber-200">Kirim Pesanan →</button>
            </div>
        </div>

        <aside class="sticky top-24 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg">
            <div class="border-b border-slate-100 p-5"><p class="text-xs font-black uppercase tracking-[.14em] text-emerald-700">Ringkasan</p><h2 class="mt-1 text-xl font-black">Pesanan Anda</h2></div>
            <div id="sidebar-items" class="max-h-72 divide-y divide-slate-100 overflow-auto px-5"></div>
            <div class="bg-emerald-950 p-5 text-white"><div class="flex items-center justify-between text-sm text-emerald-100"><span>Total jersey</span><b id="total-quantity">0 buah</b></div><div class="mt-3 flex items-end justify-between gap-3"><span class="text-sm text-emerald-100">Total</span><strong id="grand-total" class="text-2xl font-black">Rp 0</strong></div></div>
        </aside>
    </div>
</form>
@endif
<a href="{{route('jersey.lookup')}}" class="mt-8 block text-center text-sm font-bold text-emerald-800 hover:underline">Sudah pernah pesan? Cek status pesanan →</a>
</div>

@if($products->isNotEmpty())
<script>
const catalog=@json($catalog), previousItems=@json($oldItems);
const models={
    'Lelaki Pendek':{sleeve:'short',label:'Lengan pendek',image:@json(asset('images/jersey/model-pria-pendek-v2.jpg'))},
    'Lelaki Panjang':{sleeve:'long',label:'Lengan panjang',image:@json(asset('images/jersey/model-pria-panjang-v3.jpg'))},
    'Muslimah':{sleeve:'long',label:'Lengan panjang',image:@json(asset('images/jersey/model-muslimah-v3.jpg'))}
};
const container=document.getElementById('order-items'), money=value=>new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(value), html=value=>String(value).replace(/[&<>'"]/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[char]));
let currentStep=1;
function productOptions(selected){return catalog.map(p=>`<option value="${p.id}" ${String(p.id)===String(selected)?'selected':''}>${html(p.name)} — ${money(p.price)}</option>`).join('')}
function sizeButtons(product,selected){return product.sizes.map(s=>`<button type="button" data-size="${s.id}" class="rounded-xl border px-4 py-2.5 text-sm font-black transition ${String(s.id)===String(selected)?'border-emerald-700 bg-emerald-50 text-emerald-800':'border-slate-200 bg-white text-slate-600 hover:border-emerald-400'}">${html(s.name)}${Number(s.adjustment)>0?` <small>+${money(s.adjustment)}</small>`:''}</button>`).join('')}
function modelButtons(selected){return Object.entries(models).map(([name,m])=>`<button type="button" data-model="${name}" class="model-choice overflow-hidden rounded-2xl border-2 text-left transition ${name===selected?'border-emerald-700 bg-emerald-50 shadow-sm':'border-slate-200 bg-white hover:border-emerald-300'}"><img src="${m.image}" alt="" class="aspect-[4/3] w-full object-cover"><span class="block px-2 py-2 text-center text-xs font-black md:text-sm">${name}</span></button>`).join('')}
function addItem(data={}){if(container.children.length>=20)return;const product=catalog.find(p=>String(p.id)===String(data.jersey_product_id))||catalog[0],size=product.sizes.find(s=>String(s.id)===String(data.jersey_size_id))||product.sizes[0],model=models[data.model]?data.model:'Lelaki Pendek',quantity=Math.max(1,Math.min(20,Number(data.quantity)||1)),card=document.createElement('article');card.className='order-item rounded-3xl border border-slate-200 bg-white p-5 shadow-sm md:p-6';card.dataset.model=model;card.dataset.size=size.id;card.innerHTML=`<div class="flex items-center justify-between gap-3"><div><span data-role="badge" class="text-xs font-black uppercase tracking-[.14em] text-emerald-700"></span><h3 class="mt-1 text-xl font-black">Atur pilihan jersey</h3></div><button type="button" data-action="remove" class="rounded-lg px-3 py-2 text-sm font-bold text-red-600 transition hover:bg-red-50">Hapus</button></div><div class="mt-5"><span class="text-sm font-bold text-slate-700">Pilih model</span><div data-role="models" class="mt-2 grid grid-cols-3 gap-2">${modelButtons(model)}</div></div><div class="mt-5 grid gap-5 md:grid-cols-2"><label class="text-sm font-bold text-slate-700">Jenis produk<select data-role="product" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 font-normal outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100">${productOptions(product.id)}</select><span data-role="description" class="mt-1.5 block text-xs font-normal text-slate-400"></span></label><div><span class="text-sm font-bold text-slate-700">Ukuran</span><div data-role="sizes" class="mt-2 flex flex-wrap gap-2">${sizeButtons(product,size?.id)}</div></div></div><div class="mt-5 flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-slate-50 p-4"><div><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Jumlah</p><div class="mt-1 flex items-center overflow-hidden rounded-xl border border-slate-300 bg-white"><button type="button" data-action="minus" class="h-10 w-10 text-xl hover:bg-slate-50" aria-label="Kurangi jumlah">−</button><input data-role="quantity" type="number" min="1" max="20" value="${quantity}" class="h-10 w-12 border-x border-slate-200 text-center font-black [appearance:textfield]" aria-label="Jumlah jersey"><button type="button" data-action="plus" class="h-10 w-10 text-xl hover:bg-slate-50" aria-label="Tambah jumlah">+</button></div></div><div class="text-right"><p data-role="variant" class="text-xs text-slate-500"></p><p data-role="subtotal" class="mt-1 text-xl font-black text-emerald-900"></p></div></div>${['product','size','model','sleeve','quantity'].map(field=>`<input data-field="${field}" type="hidden">`).join('')}`;container.appendChild(card);bind(card);sync(card);reindex()}
function bind(card){card.querySelectorAll('[data-model]').forEach(b=>b.onclick=()=>{card.dataset.model=b.dataset.model;sync(card)});card.querySelector('[data-role="product"]').onchange=()=>{const p=selectedProduct(card);card.querySelector('[data-role="sizes"]').innerHTML=sizeButtons(p,p.sizes[0]?.id);bindSizes(card);sync(card)};bindSizes(card);card.querySelector('[data-action="minus"]').onclick=()=>changeQuantity(card,-1);card.querySelector('[data-action="plus"]').onclick=()=>changeQuantity(card,1);card.querySelector('[data-role="quantity"]').onchange=()=>sync(card);card.querySelector('[data-action="remove"]').onclick=()=>{if(container.children.length===1)return;card.remove();reindex();updateSummary()}}
function bindSizes(card){card.querySelectorAll('[data-size]').forEach(b=>b.onclick=()=>{card.dataset.size=b.dataset.size;sync(card)})}
function selectedProduct(card){return catalog.find(p=>String(p.id)===card.querySelector('[data-role="product"]').value)}
function selectedSize(card){const p=selectedProduct(card);return p.sizes.find(s=>String(s.id)===String(card.dataset.size))||p.sizes[0]}
function changeQuantity(card,delta){const input=card.querySelector('[data-role="quantity"]');input.value=Math.max(1,Math.min(20,(Number(input.value)||1)+delta));sync(card)}
function sync(card){const modelName=card.dataset.model||card.querySelector('[data-model].border-emerald-700')?.dataset.model||'Lelaki Pendek',product=selectedProduct(card),size=selectedSize(card),model=models[modelName],quantity=card.querySelector('[data-role="quantity"]');card.dataset.model=modelName;card.dataset.size=size.id;quantity.value=Math.max(1,Math.min(20,Number(quantity.value)||1));card.querySelectorAll('[data-model]').forEach(b=>{const active=b.dataset.model===modelName;b.classList.toggle('border-emerald-700',active);b.classList.toggle('bg-emerald-50',active);b.classList.toggle('shadow-sm',active);b.classList.toggle('border-slate-200',!active);b.setAttribute('aria-pressed',active)});card.querySelector('[data-role="sizes"]').innerHTML=sizeButtons(product,size.id);bindSizes(card);card.querySelector('[data-role="description"]').textContent=product.description||'Jersey resmi iSetial Wisdom';card.querySelector('[data-role="variant"]').textContent=`${model.label} · Ukuran ${size.name}`;card.querySelector('[data-role="subtotal"]').textContent=money((product.price+Number(size.adjustment))*Number(quantity.value));card.querySelector('[data-field="product"]').value=product.id;card.querySelector('[data-field="size"]').value=size.id;card.querySelector('[data-field="model"]').value=modelName;card.querySelector('[data-field="sleeve"]').value=model.sleeve;card.querySelector('[data-field="quantity"]').value=quantity.value;updateSummary()}
function reindex(){const names={product:'jersey_product_id',size:'jersey_size_id',model:'model',sleeve:'sleeve',quantity:'quantity'};[...container.children].forEach((card,i)=>{card.querySelector('[data-role="badge"]').textContent=`Jersey ${i+1}`;card.querySelector('[data-action="remove"]').classList.toggle('invisible',container.children.length===1);Object.keys(names).forEach(field=>card.querySelector(`[data-field="${field}"]`).name=`items[${i}][${names[field]}]`)})}
function itemData(card){const product=selectedProduct(card),size=selectedSize(card),quantity=Number(card.querySelector('[data-role="quantity"]').value)||1;return{product,size,quantity,model:card.dataset.model,subtotal:(product.price+Number(size.adjustment))*quantity}}
function updateSummary(){let total=0,count=0;const rows=[...container.children].map((card,i)=>{const d=itemData(card);total+=d.subtotal;count+=d.quantity;return `<div class="py-4"><div class="flex justify-between gap-3"><div><b class="text-sm">${i+1}. ${d.model}</b><p class="mt-1 text-xs text-slate-500">${html(d.product.name)} · ${html(d.size.name)} · ${d.quantity} buah</p></div><b class="whitespace-nowrap text-sm">${money(d.subtotal)}</b></div></div>`}).join('');document.getElementById('sidebar-items').innerHTML=rows;document.getElementById('grand-total').textContent=money(total);document.getElementById('total-quantity').textContent=`${count} buah`}
function renderReview(){document.getElementById('review-items').innerHTML=[...container.children].map((card,i)=>{const d=itemData(card);return `<div class="flex items-center gap-4 py-4"><img src="${models[d.model].image}" alt="" class="h-16 w-16 rounded-xl object-cover"><div class="min-w-0 flex-1"><b>${i+1}. ${d.model}</b><p class="mt-1 text-sm text-slate-500">${html(d.product.name)} · Ukuran ${html(d.size.name)} · ${d.quantity} buah</p></div><b class="whitespace-nowrap">${money(d.subtotal)}</b></div>`}).join('');const values=[['Nama',document.getElementById('customer-name').value],['Nomor HP',document.getElementById('customer-phone').value],['Alamat',document.getElementById('customer-address').value]];const list=document.getElementById('review-customer');list.innerHTML='';values.forEach(([label,value])=>{const div=document.createElement('div');div.className=label==='Alamat'?'md:col-span-2':'';const dt=document.createElement('dt');dt.className='font-bold text-slate-500';dt.textContent=label;const dd=document.createElement('dd');dd.className='mt-1 font-semibold text-slate-900';dd.textContent=value;div.append(dt,dd);list.appendChild(div)})}
function goToStep(step){if(step===3){const fields=[document.getElementById('customer-name'),document.getElementById('customer-phone'),document.getElementById('customer-address')];for(const field of fields)if(!field.checkValidity()){field.reportValidity();return}renderReview()}currentStep=step;document.querySelectorAll('[data-step-panel]').forEach(panel=>panel.hidden=Number(panel.dataset.stepPanel)!==step);document.querySelectorAll('[data-step-indicator]').forEach(indicator=>{const active=Number(indicator.dataset.stepIndicator)===step,done=Number(indicator.dataset.stepIndicator)<step;indicator.classList.toggle('bg-emerald-50',active);indicator.classList.toggle('text-emerald-800',active||done);indicator.classList.toggle('text-slate-400',!active&&!done);indicator.querySelector('span').classList.toggle('bg-emerald-800',active||done);indicator.querySelector('span').classList.toggle('border-emerald-800',active||done);indicator.querySelector('span').classList.toggle('text-white',active||done)});document.getElementById('back-button').classList.toggle('hidden',step===1);document.getElementById('next-button').classList.toggle('hidden',step===3);document.getElementById('submit-button').classList.toggle('hidden',step!==3);window.scrollTo({top:document.getElementById('jersey-form').offsetTop-90,behavior:'smooth'})}
document.getElementById('add-item').onclick=()=>addItem();document.getElementById('next-button').onclick=()=>goToStep(currentStep+1);document.getElementById('back-button').onclick=()=>goToStep(currentStep-1);document.querySelectorAll('[data-go-step]').forEach(button=>button.onclick=()=>goToStep(Number(button.dataset.goStep)));(previousItems.length?previousItems:[{}]).forEach(addItem);goToStep({{$errors->any()?2:1}});
</script>
@endif
@endsection
