// Search, category filter and sort for the item listing pages
// (swap.php, kitchen.php, clothes.php, others.php). Markup comes from includes/items.php.
(function () {
  const grid = document.getElementById('item-grid');
  if (!grid) return;

  const searchInput = document.getElementById('item-search');
  const categorySelect = document.getElementById('item-category'); // swap page only
  const sortSelect = document.getElementById('item-sort');         // sale pages only
  const countText = document.getElementById('item-count');
  const noResults = document.getElementById('item-no-results');
  const cards = Array.from(grid.querySelectorAll('.item-card'));
  const noun = grid.dataset.noun;
  const nounPlural = grid.dataset.nounPlural;

  function applyFilters() {
    const words = searchInput.value.toLowerCase().trim().split(/\s+/).filter(Boolean);
    const category = categorySelect ? categorySelect.value : '';
    let shown = 0;

    cards.forEach(function (card) {
      const matchesCategory = !category || card.dataset.category === category;
      const matchesSearch = words.every(function (w) { return card.dataset.search.includes(w); });
      const visible = matchesCategory && matchesSearch;
      card.hidden = !visible;
      if (visible) shown++;
    });

    const filtering = words.length > 0 || category !== '';
    countText.textContent = filtering
      ? 'Showing ' + shown + ' of ' + cards.length + ' ' + nounPlural
      : cards.length + ' ' + (cards.length === 1 ? noun : nounPlural);
    noResults.hidden = shown !== 0;
    grid.hidden = shown === 0;
  }

  function applySort() {
    const mode = sortSelect.value;
    const id = function (card) { return Number(card.dataset.id); };
    const price = function (card) { return Number(card.dataset.price); };

    cards.slice().sort(function (a, b) {
      if (mode === 'price-asc') return price(a) - price(b) || id(b) - id(a);
      if (mode === 'price-desc') return price(b) - price(a) || id(b) - id(a);
      return id(b) - id(a); // newest first
    }).forEach(function (card) { grid.appendChild(card); });
  }

  searchInput.addEventListener('input', applyFilters);
  if (categorySelect) categorySelect.addEventListener('change', applyFilters);
  if (sortSelect) sortSelect.addEventListener('change', applySort);
})();
