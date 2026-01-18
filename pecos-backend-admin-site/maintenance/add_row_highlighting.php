<?php
/**
 * Add row highlighting to remaining admin blade files
 */

$files = [
    'C:/xampp/htdocs/pecos-backend-admin-site/resources/views/admin/users.blade.php',
    'C:/xampp/htdocs/pecos-backend-admin-site/resources/views/admin/blog.blade.php',
    'C:/xampp/htdocs/pecos-backend-admin-site/resources/views/admin/events.blade.php',
    'C:/xampp/htdocs/pecos-backend-admin-site/resources/views/admin/reviews.blade.php',
    'C:/xampp/htdocs/pecos-backend-admin-site/resources/views/admin/coupons.blade.php',
    'C:/xampp/htdocs/pecos-backend-admin-site/resources/views/admin/gift-cards.blade.php',
];

$styleScript = '

<style>
.table tbody tr.row-selected td {
    background-color: #0d6efd !important;
    color: white !important;
}
.table tbody tr.row-selected td strong {
    color: white !important;
}
.table tbody tr:hover:not(.row-selected) td {
    background-color: #f8f9fa;
}
</style>

<script>
function highlightRow(event) {
    var target = event.target;
    var row = target.closest(\'tr\');
    if (!row) return;
    if (target.tagName === \'BUTTON\' || target.tagName === \'A\' || target.tagName === \'SELECT\' ||
        target.tagName === \'I\' || target.closest(\'button\') || target.closest(\'a\') || target.closest(\'select\')) {
        return;
    }
    var selectedRows = document.querySelectorAll(\'.table tbody tr.row-selected\');
    selectedRows.forEach(function(r) {
        r.classList.remove(\'row-selected\');
    });
    row.classList.add(\'row-selected\');
}
</script>
';

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "File not found: $file\n";
        continue;
    }

    $content = file_get_contents($file);

    // Check if already has the script
    if (strpos($content, 'function highlightRow') !== false) {
        echo "Already processed: $file\n";
        continue;
    }

    // Add onclick to <tr> tags in forelse loops
    $content = preg_replace(
        '/@forelse\([^)]+\)\s+<tr>/',
        '@forelse($1)' . "\n" . '            <tr onclick="highlightRow(event)" style="cursor: pointer;">',
        $content
    );

    // Add style and script before @endsection
    $content = preg_replace(
        '/@endsection/',
        $styleScript . '@endsection',
        $content,
        1  // Only replace first occurrence
    );

    file_put_contents($file, $content);
    echo "Processed: $file\n";
}

echo "\nDone! Run 'php artisan view:clear' to clear cached views.\n";
