<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Услуги</title>
    <link rel="stylesheet" href="css/service.css">
</head>
<body>
    <header>
    <?php include "header.php";?>
    </header>

    <?php
    include "connect.php";

    $sql = "select service_name, price, description, department.department_name from service
            join specialization on specialization.id_specialization=service.id_specialization
            join department on department.id_department=specialization.id_department
            order by department_name";
    $result = $connect->query($sql);

    $services = [];
     if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
               $services[$row['department_name']][] = $row;
       }
    }  ?>

    <table>
        <thead>
            <tr>
                <th>Отделение</th>
                <th>Наименование услуги</th>
                <th>Описание</th>
                <th>Стоимость</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $department => $department_services): ?>
                <tr>
                    <td rowspan="<?php echo count($department_services); ?>"><?php echo htmlspecialchars($department); ?></td>
                    <td><?php echo htmlspecialchars($department_services[0]['service_name']); ?></td>
                    <td><?php echo htmlspecialchars($department_services[0]['description']); ?></td>
                    <td><?php echo htmlspecialchars($department_services[0]['price']); ?> ₽</td>
                </tr>
                <?php for ($i = 1; $i < count($department_services); $i++): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($department_services[$i]['service_name']); ?></td>
                        <td><?php echo htmlspecialchars($department_services[$i]['description']); ?></td>
                        <td><?php echo htmlspecialchars($department_services[$i]['price']); ?> ₽</td>
                    </tr>
                <?php endfor; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <footer>
        <?php include "footer.php";?>
    </footer>
    </body>
    </html>