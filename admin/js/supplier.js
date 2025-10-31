/* Supplier page demo interactions */
(function () {
  const form = document.getElementById('supplierForm');
  const tableBody = document.getElementById('supplierTableBody');

  if (form && tableBody) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const company = document.getElementById('companyName').value.trim();
      const person = document.getElementById('personName').value.trim();
      const phone = document.getElementById('phone').value.trim();
      const email = document.getElementById('email').value.trim();

      if (!company || !person || !phone) return;

      const row = document.createElement('tr');
      row.className = 'border-b border-border-light hover:bg-gray-50';
      row.innerHTML = `
        <td class="py-3 px-4 text-heading-light font-medium">${company}</td>
        <td class="py-3 px-4 text-text-light">${person}</td>
        <td class="py-3 px-4 text-text-light">${phone}</td>
        <td class="py-3 px-4 text-text-light">${email || ''}</td>
        <td class="py-3 px-4">
          <div class="flex items-center gap-2">
            <button class="text-primary hover:text-primary/80 p-2 rounded-lg hover:bg-primary/10 transition-colors" title="Edit">
              <span class="material-icons text-lg">edit</span>
            </button>
            <button class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
              <span class="material-icons text-lg">delete</span>
            </button>
          </div>
        </td>
      `;
      tableBody.prepend(row);
      form.reset();
    });
  }
})();


