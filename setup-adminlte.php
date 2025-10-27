<?php

function downloadAdminLTE() {
    $version = '3.2.0';
    $url = "https://github.com/ColorlibHQ/AdminLTE/archive/refs/tags/v{$version}.zip";
    $zipFile = "adminlte-{$version}.zip";
    $extractPath = public_path('vendor');
    
    // Create vendor directory if it doesn't exist
    if (!is_dir($extractPath)) {
        mkdir($extractPath, 0755, true);
    }
    
    echo "Downloading AdminLTE v{$version}...\n";
    
    // Download the file
    $zipContent = file_get_contents($url);
    file_put_contents($zipFile, $zipContent);
    
    echo "Extracting...\n";
    
    // Extract the zip file
    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo($extractPath);
        $zip->close();
        
        // Rename the extracted folder
        $extractedFolder = $extractPath . "/AdminLTE-{$version}";
        $targetFolder = $extractPath . "/adminlte";
        
        if (is_dir($extractedFolder)) {
            rename($extractedFolder, $targetFolder);
            echo "AdminLTE installed successfully to: " . $targetFolder . "\n";
        }
        
        // Clean up
        unlink($zipFile);
    } else {
        echo "Failed to extract AdminLTE\n";
    }
}

// Run the function
downloadAdminLTE();