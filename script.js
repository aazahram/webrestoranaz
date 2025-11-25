// Filter menu
const filterButtons = document.querySelectorAll('.filter');
const menuCards = document.querySelectorAll('.menu-card');

filterButtons.forEach(button => {
    button.addEventListener('click', () => {
        const category = button.getAttribute('data-category');
        filterButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
        
        menuCards.forEach(card => {
            if (category === 'semua' || card.dataset.category === category) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// Keranjang belanja
let keranjang = [];

function tambahKeranjang(id, nama, harga) {
    let item = keranjang.find(m => m.id === id);
    if (item) {
        item.jumlah++;
    } else {
        keranjang.push({ id, nama, harga, jumlah: 1 });
    }
    tampilkanKeranjang();
    showNotification(`${nama} ditambahkan ke keranjang!`);
}

function tampilkanKeranjang() {
    const container = document.getElementById('keranjang-items');
    const totalElement = document.getElementById('total-harga');
    let total = 0;

    if (keranjang.length === 0) {
        container.innerHTML = '<p class="empty-cart">Keranjang masih kosong</p>';
        totalElement.textContent = '0';
        return;
    }

    container.innerHTML = '';
    keranjang.forEach((item, index) => {
        const itemTotal = item.harga * item.jumlah;
        total += itemTotal;
        
        const itemElement = document.createElement('div');
        itemElement.className = 'cart-item';
        itemElement.innerHTML = `
            <div class="item-info">
                <strong>${item.nama}</strong>
                <br>
                <small>Rp ${item.harga.toLocaleString('id-ID')} x ${item.jumlah}</small>
            </div>
            <div class="item-controls">
                <button class="small-btn" onclick="ubahJumlah(${index}, -1)">−</button>
                <span>${item.jumlah}</span>
                <button class="small-btn" onclick="ubahJumlah(${index}, 1)">+</button>
                <button class="remove-btn" onclick="hapusItem(${index})">🗑️</button>
            </div>
            <div class="item-price">Rp ${itemTotal.toLocaleString('id-ID')}</div>
        `;
        container.appendChild(itemElement);
    });

    totalElement.textContent = total.toLocaleString('id-ID');
}

function ubahJumlah(index, delta) {
    keranjang[index].jumlah += delta;
    if (keranjang[index].jumlah <= 0) {
        keranjang.splice(index, 1);
    }
    tampilkanKeranjang();
}

function hapusItem(index) {
    const itemName = keranjang[index].nama;
    keranjang.splice(index, 1);
    tampilkanKeranjang();
    showNotification(`${itemName} dihapus dari keranjang`);
}

function kosongkanKeranjang() {
    if (keranjang.length === 0) return;
    
    if (confirm('Yakin ingin mengosongkan keranjang?')) {
        keranjang = [];
        tampilkanKeranjang();
        showNotification('Keranjang dikosongkan');
    }
}

function checkout() {
    if (keranjang.length === 0) {
        alert("Keranjang masih kosong! Silakan tambah menu terlebih dahulu.");
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'checkout.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert(`✅ Pesanan berhasil!\nTotal: Rp ${response.total.toLocaleString('id-ID')}\nID Pesanan: ${response.order_id}`);
                        keranjang = [];
                        tampilkanKeranjang();
                        
                        // Redirect ke riwayat setelah 2 detik
                        setTimeout(() => {
                            window.location.href = 'riwayat.php';
                        }, 2000);
                    } else {
                        alert('❌ Gagal membuat pesanan: ' + response.message);
                    }
                } catch (e) {
                    alert('❌ Terjadi kesalahan saat memproses pesanan');
                    console.error('Error:', e);
                }
            } else {
                alert('❌ Terjadi kesalahan jaringan');
            }
        }
    };
    
    xhr.send(JSON.stringify(keranjang));
}

function showNotification(message) {
    // Buat notifikasi sederhana
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: #4CAF50;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// CSS untuk animasi notifikasi
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
`;
document.head.appendChild(style);