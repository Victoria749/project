<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Специалисты</title>
    <link rel="stylesheet" href="css/doctor.css">
</head>
<body>
    <header>
    <?php include "header.php";?>
    </header>

    <?php 
    include "connect.php";
    
    $sql = "select last_name,doctor.name,father_name,work_experience,specialization.specialization_name
            from doctor
            join specialization on specialization.id_specialization=doctor.id_specialization
            order by specialization.specialization_name";
    $result = $connect->query($sql);


    $doctors = [];
       if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
               $doctors[$row['specialization_name']][] = $row;
            }
        }
    
    ?>


    <h1>Список специалистов</h1>
    <section id="doctor-list">
        <?php foreach ($doctors as $specialty => $doctorList): ?>
            <h2><?php echo htmlspecialchars($specialty); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Фамилия</th>
                        <th>Имя</th>
                        <th>Отчество</th>
                        <th>Стаж работы (лет)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($doctorList as $doctor): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($doctor['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($doctor['name']); ?></td>
                            <td><?php echo htmlspecialchars($doctor['father_name']); ?></td>
                            <td><?php echo htmlspecialchars($doctor['work_experience']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    </section>

    <footer>
        <?php include "footer.php";?>
    </footer>
    </body>
    </html>