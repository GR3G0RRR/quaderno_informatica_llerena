<!DOCTYPE html>
<html>
<body>
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