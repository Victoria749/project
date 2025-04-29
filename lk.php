<?php
session_start();
include "connect.php";
//проверка, авторизован пользователь или нет
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
}
//получение данных об авторизованном пользователе
$user_id = $_SESSION['user_id'];
$stmt = $connect->prepare("select last_name, name, father_name from user where id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "Пользователь не найден.";
}
//массив для хранения услуг
$services = [];
$result = $connect->query("select id_service, service_name, price from service");
while ($row = $result->fetch_assoc()) {
    $services[] = $row;
}
//массив для хранения отделений
$departments = [];
$result = $connect->query("select id_department, department_name from department");
while ($row = $result->fetch_assoc()) {
    $departments[] = $row;
}
//массив для хранения записей на прием
$appointments = [];
$result = $connect->query("select service.service_name,date,time,status from appointment
                          join service on service.id_service=appointment.id_service
                          where id_user=$user_id");
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

$selected_services = $_SESSION['selected_services'] ?? [];
$total_cost = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //фильтрация услуг по отделению
    if(isset($_POST['filter'])){
        $selected_department_id = isset($_POST['department']) ? $_POST['department'] : null;
       if ($selected_department_id) {
           $stmt = $connect->prepare("select id_service, service_name, price from service
                                      join specialization on 
                                      specialization.id_specialization = service.id_specialization
                                      where specialization.id_department = ?");
           $stmt->bind_param("i", $selected_department_id);
           $stmt->execute();
           $result = $stmt->get_result();
           $services = [];
           while ($row = $result->fetch_assoc()) {
               $services[] = $row;
           }
        }
       else {
           $result = $connect->query("select id_service, service_name, price from service");
            while ($row = $result->fetch_assoc()) {
                $services[] = $row;
            }
        }
    }
    //получение врачей по выбранной специальности
    if (isset($_POST['service'])){
        $selected_service_id = isset($_POST['service']) ? $_POST['service'] : null;
        $doctors = [];
          if ($selected_service_id) {
              $stmt = $connect->prepare("select id_doctor, last_name, name, father_name
                                         from doctor
                                         join service on service.id_specialization = doctor.id_specialization 
                                         where service.id_service = ?");
              $stmt->bind_param("i", $selected_service_id);
              $stmt->execute();
              $result = $stmt->get_result();
    
              while ($row = $result->fetch_assoc()) {
                  $doctors[] = $row;
              }
          }  
    }
    //добавление услуги для записи на прием
    if (isset($_POST['add_service'])) {
        $service_id = $_POST['service'];
        $doctor_id = $_POST['doctor'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        if ($service_id && $doctor_id && $date && $time) {
            $stmt = $connect->prepare("select service_name, price, doctor.last_name from service
                                       join specialization on specialization.id_specialization=service.id_specialization
                                       join doctor on doctor.id_specialization=specialization.id_specialization WHERE id_service = ?");
            $stmt->bind_param("i", $service_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $service_info = $result->fetch_assoc();
            $selected_services[] = [
                'service_id' => $service_id,
                'service_name' => $service_info['service_name'],
                'doctor_id' => $doctor_id,
                'last_name' => $service_info['last_name'],
                'date' => $date,
                'time' => $time,
                'price' => $service_info['price']
            ];
            $_SESSION['selected_services'] = $selected_services; 
        }
    }
    //добавление данных о записи на прием в бд
    if (isset($_POST['sign'])) {
        foreach ($selected_services as $service) {
                 $service_id = $service['service_id'];
                 $doctor_id = $service['doctor_id'];
                 $date = $service['date'];
                 $time = $service['time'];
            if (!empty($service_id) && !empty($doctor_id) && !empty($date) && !empty($time)) {
                try {
                    $stmt = $connect->prepare("insert into appointment(date, time, id_user, id_service, id_doctor)
                                               values (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssiii", $date, $time, $user_id, $service_id, $doctor_id);
                       if ($stmt->execute()) {
                           $_SESSION['message'] = "Запись отправлена на рассмотрение";
                       } else {
                        $_SESSION['message'] = "Ошибка при записи";
                       }
                } catch (mysqli_sql_exception $e) {
                    $_SESSION['message'] = "Выбранная услуга занята в выбранное время";
                }
            }
            else{
                $_SESSION['message'] = "Заполните все поля";
            }
        }
        unset($_SESSION['selected_services']); 
        header('Location: lk.php');
        exit(); 
    }
}
//формирование отчета
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report'])) {
    $stmt_user = $connect->prepare("select last_name, name, father_name from user where id_user = ?");
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user_info = $result_user->fetch_assoc();
    $full_name = $user_info['last_name'] . ' ' . mb_substr($user_info['name'], 0, 1) . '. ' . mb_substr($user_info['father_name'], 0, 1) . '.';
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
    $sql = "select date, service.service_name, service.price from appointment
            join service on service.id_service = appointment.id_service
            where id_user = ? and date between ? and ?
            group by date, service_name, price order by date";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("iss", $user_id, $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    require('tfpdf.php'); 
    define('FPDF_FONTPATH', 'font/');  
    $pdf = new TFPDF();
    $pdf->AddPage();
    $pdf->AddFont('DejaVu','','DejaVuSans.ttf', true); 
    $pdf->SetFont('DejaVu', '', 16); 
    $pdf->Cell(0, 10, 'Отчет по услугам', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('DejaVu', '', 12);
    $pdf->Cell(0, 10, 'ФИО: ' . $full_name, 0, 1);
    $pdf->Ln(5);
    $pdf->SetFont('DejaVu', '', 12);
    $pdf->Cell(40, 10, 'Дата', 1);
    $pdf->Cell(100, 10, 'Услуга', 1);
    $pdf->Cell(40, 10, 'Цена', 1);
    $pdf->Ln();
    $pdf->SetFont('DejaVu', '', 12);
    $current_date = '';
    $total_price_per_day = 0;
    while ($row = $result->fetch_assoc()) {
           $date = $row['date'];
             if ($current_date !== $date) {
                 if ($current_date !== '') {
                     $pdf->Cell(140, 10, 'Общая сумма за ' . $current_date . ':', 1);
                     $pdf->Cell(40, 10, number_format($total_price_per_day, 2) . ' руб.', 1);
                     $pdf->Ln();
                 }
               $current_date = $date;
               $total_price_per_day = 0; 
             }
           $pdf->Cell(40, 10, $date, 1);
           $pdf->Cell(100, 10, $row['service_name'], 1);
           $pdf->Cell(40, 10, number_format($row['price'], 2) . ' руб.', 1);
           $pdf->Ln();
           $total_price_per_day += $row['price'];
    }
    if ($current_date !== '') {
        $pdf->Cell(140, 10, 'Общая сумма за ' . $current_date . ':', 1);
        $pdf->Cell(40, 10, number_format($total_price_per_day, 2) . ' руб.', 1);
        $pdf->Ln();
    }
    $pdf->Output('D', 'report.pdf');
} 
//обработка сообщений
$message='';
if(isset($_SESSION['message'])){
    $message=$_SESSION['message'];
    unset($_SESSION['message']);
}

$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'appointment';

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/lk.css">
    <title>Личный кабинет пациента</title>
    
    <script>
        function validateDate() {
            const dateInput = document.getElementById('date');
            const selectedDate = new Date(dateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const day = selectedDate.getDay();

            if (selectedDate < today) {
            alert("Выберите дату, которая не является прошедшей.");
            dateInput.value = ''; 
            return false;
            }
            if (day === 0 || day === 6) {
                alert("Выберите рабочий день (понедельник - пятница).");
                dateInput.value = ''; 
                return false;
            }
            return true;
        }

        function populateTimeSlots() {
            const timeSelect = document.getElementById('time');
            timeSelect.innerHTML = ''; 

            const startHour = 8; 
            const endHour = 18; 

            for (let hour = startHour; hour < endHour; hour++) {
                if (hour === 13) continue; 
                const timeValue = (hour < 10 ? '0' + hour : hour) + ':00';
                const option = document.createElement('option');
                option.value = timeValue;
                option.textContent = timeValue;
              timeSelect.appendChild(option);

            }
        }
        window.onload = function() {
           
            populateTimeSlots();
        };
    </script>
   
</head>
<body>

    <div class="header">
        <h1><?php echo htmlspecialchars($user['last_name'] . ' ' . $user['name'] . ' ' . $user['father_name']); ?></h1>
        <a href="logout.php" class="account-link">
                <span>Выйти</span>
                <img src="image/logout.png" alt="Выход" class="account-icon">
            </a>
    </div>

    <div class="tabs">
    <a href="?tab=appointment" class="<?= $activeTab === 'appointment' ? 'active' : '' ?>">Запись на прием</a>
    <a href="?tab=services" class="<?= $activeTab === 'services' ? 'active' : '' ?>">Мои записи</a>
    </div>

    <div class="tab <?= $activeTab === 'appointment' ? 'active' : '' ?>">

      <?php if (!empty($message)): ?>
               <div class="notification show" role="alert">
               <?php echo htmlspecialchars($message); ?>
               <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="this.parentElement.style.display='none';">
                  <span aria-hidden="true">&times;</span>
               </button>
               </div>
      <?php endif; ?>
        
      <form method="post" action="" class="depart">

            <label for="department">Отделение:</label>
            <select name="department" id="department">
            <option value="">Все отделения</option>
               <?php foreach ($departments as $department): ?>
                <option value="<?= htmlspecialchars($department['id_department']) ?>" <?= (isset($selected_department_id) && $selected_department_id == $department['id_department']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($department['department_name']) ?>
                </option>
               <?php endforeach; ?>
            </select>
            <button type="submit" class="filter" name="filter">Применить фильтр</button>

      </form>
      <br>

      <form method="post" action="" onsubmit="return validateDate();" class="fill">
      
            <label for="service">Услуга:</label>
            <select name="service" id="service" onchange="this.form.submit()">
            <option value="">Выберите услугу</option>
               <?php foreach ($services as $service): ?>
                <option value="<?= $service['id_service'] ?>" <?= (isset($selected_service_id) && $selected_service_id == $service['id_service']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($service['service_name']) ?>
                </option>
               <?php endforeach; ?>
            </select>
            <br>

            <label for="doctor">Врач:</label>
            <select name="doctor" id="doctor" required>
            <option value="">Сначала выберите услугу</option>
              <?php if (!empty($doctors)): ?>
                <?php foreach ($doctors as $doctor): ?>
                 <option value="<?= $doctor['id_doctor'] ?>">
                 <?= htmlspecialchars($doctor['last_name']) . ' ' . htmlspecialchars($doctor['name']) . ' ' . htmlspecialchars($doctor['father_name']) ?>
                 </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
            <br>

            <label for="date">Дата:</label>
            <input type="date" id="date" name="date" required>
            <br>

            <label for="time">Время:</label>
            <select id="time" name="time" required></select>
            <br>

            <button type="submit" class="add_service" name="add_service">Добавить услугу</button>

       </form> 
       <br>
       
       <form method="post" action="" class="sign">
             <table id="selected-services-table" class="sel_ser">
             <thead>
                   <tr>
                       <th class="sign">Услуга</th>
                       <th class="sign">Врач</th>
                       <th class="sign">Дата</th>
                       <th class="sign">Время</th>
                       <th class="sign">Цена</th>
                   </tr>
             </thead>

             <tbody>
             <?php if (!empty($selected_services)): ?>
                   <?php foreach ($selected_services as $service): ?>
                   <tr>
                      <td><?= htmlspecialchars($service['service_name']) ?></td>
                      <td><?= htmlspecialchars($service['last_name']) ?></td>
                      <td><?= htmlspecialchars($service['date']) ?></td>
                      <td><?= htmlspecialchars($service['time']) ?></td>
                      <td><?= htmlspecialchars($service['price']) ?></td>
                   </tr>
                   <?php endforeach; ?>
             <?php else: ?>
                   <tr>
                      <td colspan="5">Нет выбранных услуг</td>
                   </tr>
             <?php endif; ?>
             </tbody>
             </table>
             <br>

             <div>
                 <strong>Общая стоимость: </strong><span><?= array_sum(array_column($selected_services, 'price')) ?></span>
             </div>
             <br>
             
             <button class="sign" type="submit" id="sign" name="sign" onclick="return confirm('Вы уверены, что хотите записаться на прием?');"<?= empty($selected_services) ? 'disabled' : '' ?>>Записаться</button>
            
       </form>

    </div>


    <div class="tab <?= $activeTab === 'services' ? 'active' : '' ?>">

    <h2>Сформировать отчет о посещенных услугах</h2>
         <form method="post" class="report">

            <label for="start_date">Дата начала:</label>
            <input type="date" name="start_date" class ="start_date" id="start_date" required>
            <br>

            <label for="end_date">Дата окончания:</label>
            <input type="date" name="end_date" class ="end_date" id="end_date" required>
            <br>

            <button type="submit" class="report" name="report">Скачать отчет</button>

         </form>

    <table border="1">
        <tr>
            <th>Услуга</th>
            <th>Дата</th>
            <th>Время</th>
            <th>Статус</th>
        </tr>
        <?php if (!empty($appointments)): ?>
            <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?= htmlspecialchars($appointment['service_name']) ?></td>
                    <td><?= htmlspecialchars($appointment['date']) ?></td>
                    <td><?= htmlspecialchars($appointment['time']) ?></td>
                    <td><?= htmlspecialchars($appointment['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">Нет записей на прием.</td>
            </tr>
        <?php endif; ?>
    </table>
    </div>
    
</body>
</html>