<!DOCTYPE html>
<html lang="ita">
    <head>
        <meta charset="UTF-8">
        <meta name = "viewport" content="width=device-width, initial-scale=1.0">
        <title>esercizio1_1</title>
    </head>

<body>
    <h1>esercizio 1:</h1>
    <hr>
    <h2>TABELLA PITTAGORICA</h2>
<table border = 3>
<?php
$size = 10;
echo "<tr><th>*</th>";
for($i = 1; $i <= $size; $i++){
    echo "<th>$i</th>";
}
echo "</tr>";

for($i = 1; $i <= $size; $i++){
    echo "<tr>";
    echo "<th>$i</th>";
    for($j = 1; $j <= $size; $j++){
        echo "<td>". ($i * $j)."</td>";
    }
    echo "</tr>";
}
?>
</table>

</body>
</html>