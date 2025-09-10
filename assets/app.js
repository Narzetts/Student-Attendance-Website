// app.js
async function getLocation() {
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) return reject('GPS tidak tersedia');
    navigator.geolocation.getCurrentPosition(pos => {
      resolve({lat: pos.coords.latitude.toFixed(7), lng: pos.coords.longitude.toFixed(7)});
    }, err => reject(err.message || 'Izin lokasi ditolak'));
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const locBtns = document.querySelectorAll('[data-get-loc]');
  locBtns.forEach(b => b.addEventListener('click', async (e) => {
    e.preventDefault();
    const targetLat = document.querySelector('#lat');
    const targetLng = document.querySelector('#lng');
    try{
      b.textContent = 'Mencari lokasi...';
      const loc = await getLocation();
      targetLat.value = loc.lat;
      targetLng.value = loc.lng;
      b.textContent = 'Lokasi ditemukan';
      setTimeout(()=> b.textContent = 'Perbarui Lokasi', 1500);
    }catch(err){
      alert('Gagal dapat lokasi: ' + err);
      b.textContent = 'Perbarui Lokasi';
    }
  }));
});
