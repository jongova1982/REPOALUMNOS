document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('imagen');
  const preview = document.getElementById('preview');
  if (!input || !preview) return;
  input.addEventListener('change', () => {
    const file = input.files[0];
    if (!file) { preview.src=''; preview.classList.add('hidden'); return; }
    const allowed = ['image/jpeg','image/png','image/webp'];
    if (!allowed.includes(file.type) || file.size > 5*1024*1024) {
      alert('Seleccione JPG, JPEG, PNG o WEBP de máximo 5 MB.');
      input.value=''; preview.src=''; preview.classList.add('hidden'); return;
    }
    preview.src = URL.createObjectURL(file);
    preview.classList.remove('hidden');
  });
});
