<?php
session_start();
include "connect.php";

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
}

$userInfo = null;
if (isset($_POST['id_user'])) {
    $id_user = intval($_POST['id_user']);
    $stmt = $connect->prepare("select * from user where id_user = ? and 
    role != 'Администратор' order by last_name");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $userInfo = $result->fetch_assoc();
    }
    $stmt->close();
}
//поиск пользователей
$searchQuery = isset($_POST['search']) ? trim($_POST['search']) : '';
$foundUsers = [];
$allUsers = [];
$result_user = $connect->query("select id_user, last_name, name, 
father_name from user where role!='Администратор' order by last_name");

if ($result_user->num_rows > 0) {
    while ($row_user = $result_user->fetch_assoc()) {
        if ($searchQuery && stripos($row_user['last_name'] . ' ' . $row_user['name'] . ' ' . 
        $row_user['father_name'], $searchQuery) !== false) {
            $foundUsers[] = $row_user;} 
            else {
            $allUsers[] = $row_user;}
    }
}

if (isset($_POST['id_appointment'])) {
    $id_appointment = intval($_POST['id_appointment']);
    $new_status = $_POST['status'];
    $stmt = $connect->prepare("UPDATE appointment SET status = ? WHERE id_appointment = ?");
    $stmt->bind_param("si", $new_status, $id_appointment);
    $stmt->execute();
    $stmt->close();
}
//сортировка записей
$order_by = 'date'; 
if (isset($_GET['sort'])) {
    $order_by = $_GET['sort'];
}

$allowed_sort_columns = ['date', 'service_name', 'last_name', 'status'];

if (!in_array($order_by, $allowed_sort_columns)) {
    $order_by = 'date'; 
}

$appointment = $connect->query("select service.service_name,date,time_format(time, '%k:%i') as time,
                             status,user.last_name,user.name,user.father_name,id_appointment from appointment
                             join service on service.id_service=appointment.id_service
                             join user on user.id_user=appointment.id_user
                             where status = 'В обработке'
                             order by $order_by"); 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_service'])) {
    $service_name = $_POST['service_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $id_specialization = $_POST['id_specialization'];
    $stmt = $connect->prepare("insert into service (service_name, price, 
    description, id_specialization) values (?, ?, ?, ?)");
    $stmt->bind_param("sdsi", $service_name, $price, $description, $id_specialization);
    $stmt->execute();
}
//редактирование услуг
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_service'])) {
    $id_service = $_POST['id_service'];
    $service_name = $_POST['service_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $id_specialization = $_POST['id_specialization'];
    $stmt = $connect->prepare("update service set service_name = ?, price = ?, description = ?, 
    id_specialization = ? where id_service = ?");
    $stmt->bind_param("sdssi", $service_name, $price, $description, $id_specialization, $id_service);
    $stmt->execute();
}
//удаление услуг
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_service'])) {
    $id_service = $_POST['id_service'];
    $check_stmt = $connect->prepare("select count(*) from appointment where id_service = ?");
    $check_stmt->bind_param("i", $id_service);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();
    if($count > 0){
        $_SESSION['message'] = "Услуга не может быть удалена, так как она уже используется.";
    }
    else{
    $stmt = $connect->prepare("delete from service where id_service = ?");
    $stmt->bind_param("i", $id_service);
    $stmt->execute();}
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report'])) {
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
    $sql = "select date, specialization.specialization_name, count(id_user) as user_count 
            from appointment
            join doctor on doctor.id_doctor = appointment.id_doctor
            join specialization on specialization.id_specialization = doctor.id_specialization
            where date between ? and ?
            group by date, specialization.specialization_name 
            order by date";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    require('tfpdf.php'); 
    define('FPDF_FONTPATH', 'font/');  
    $pdf = new TFPDF();
    $pdf->AddPage();
    $pdf->AddFont('DejaVu','','DejaVuSans.ttf', true); 
    $pdf->SetFont('DejaVu', '', 16); 
    $pdf->Cell(0, 10, 'Отчет о посещениях врачей', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('DejaVu', '', 12);
    $pdf->Cell(40, 10, 'Дата', 1);
    $pdf->Cell(80, 10, 'Специальность', 1);
    $pdf->Cell(70, 10, 'Количество пациентов', 1);
    $pdf->Ln();
    $pdf->SetFont('DejaVu', '', 12);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(40, 10, $row['date'], 1);
        $pdf->Cell(80, 10, $row['specialization_name'], 1);
        $pdf->Cell(70, 10, $row['user_count'], 1);
        $pdf->Ln();
    }
    $pdf->Output('D', 'report.pdf');
} 

$specializations = [];
$result = $connect->query("select id_specialization,specialization_name from specialization");
while ($row = $result->fetch_assoc()) {
    $specializations[] = $row;
}

$services = [];
$result = $connect->query("select id_service,service_name,price,description,specialization_name,service.id_specialization
from service
join specialization on specialization.id_specialization=service.id_specialization
order by service_name");
while ($row = $result->fetch_assoc()) {
    $services[] = $row;
}
$message = '';
if(isset($_SESSION['message'])){
    $message=$_SESSION['message'];
    unset($_SESSION['message']);
}

$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'users';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin.css">
    <title>Панель администратора</title>
   
</head>
<body>

    <div class="header">
        <h1>Администратор</h1>
        <a href="logout.php" class="account-link">
                <span>Выйти</span>
                <img src="image/logout.png" alt="Выход" class="account-icon">
            </a>
    </div>

    <div class="tabs">
    <a href="?tab=users" class="<?= $activeTab === 'users' ? 'active' : '' ?>">Пользователи</a>
    <a href="?tab=appoints" class="<?= $activeTab === 'appoints' ? 'active' : '' ?>">Записи</a>
    <a href="?tab=services" class="<?= $activeTab === 'services' ? 'active' : '' ?>">Услуги</a>
    </div>

<div class="tab <?= $activeTab === 'users' ? 'active' : '' ?>"> 
        
       <form method="post" action="">
         <input type="text" name="search" class="search" placeholder="Поиск по имени или фамилии..." value="<?php echo htmlspecialchars($searchQuery); ?>">
         <button type="submit" class="search"><img src="image/search.png" alt="Поиск" class="search-icon"></button>
       </form>

       <ul>
           <?php if (!empty($foundUsers)): ?>
                 <h2>Найденные пользователи:</h2>
           <?php foreach ($foundUsers as $row_user): ?>
                 <li>
                 <form method="post" action="">
                    <button type="submit" class="user" name="id_user" value="<?php echo $row_user['id_user']; ?>">
                        <?php echo htmlspecialchars($row_user['last_name'] . ' ' . $row_user['name'] . ' ' . $row_user['father_name']); ?>
                    </button>
                 </form>
                 </li>
           <?php endforeach; ?>
           <?php else: ?>
              <?php if ($searchQuery): ?>
              <p class="no-results">Пользователи не найдены по вашему запросу "<?php echo htmlspecialchars($searchQuery); ?>"</p>
           <?php endif; ?>
           <?php endif; ?>

           <?php if (!empty($allUsers)): ?>
                 <h2>Все пользователи:</h2>
           <?php foreach ($allUsers as $row_user): ?>
                 <li>
                 <form method="post" action="">
                 <button type="submit" class="user" name="id_user" value="<?php echo $row_user['id_user']; ?>">
                        <?php echo htmlspecialchars($row_user['last_name'] . ' ' . $row_user['name'] . ' ' . $row_user['father_name']); ?>
                 </button>
                 </form>
                 </li>
           <?php endforeach; ?>
           <?php else: ?>
                 <li>"Нет пользователей"</li>
           <?php endif; ?>
       </ul>
       
       <?php if ($userInfo): ?>
           <h2>Информация о пользователе</h2>
           <p><strong>Фамилия:</strong> <?php echo htmlspecialchars($userInfo['last_name']); ?></p>
           <p><strong>Имя:</strong> <?php echo htmlspecialchars($userInfo['name']); ?></p>
           <p><strong>Отчество:</strong> <?php echo htmlspecialchars($userInfo['father_name']); ?></p>
           <p><strong>Дата рождения:</strong> <?php echo htmlspecialchars($userInfo['date_of_birth']); ?></p>
           <p><strong>Телефон:</strong> <?php echo htmlspecialchars($userInfo['number']); ?></p>
           <p><strong>Номер страхового полиса:</strong> <?php echo htmlspecialchars($userInfo['number_polis']); ?></p>
           <p><strong>Почта:</strong> <?php echo htmlspecialchars($userInfo['email']); ?></p>
       <?php endif; ?>

</div>


<div id="appoints" class="tab <?= $activeTab === 'appoints' ? 'active' : '' ?>">

         <h1>Сформировать отчет о посещениях</h1>
         <form method="post" class="report">

            <label for="start_date">Дата начала:</label>
            <input type="date" name="start_date" class ="start_date" id="start_date" required>
            <br>

            <label for="end_date">Дата окончания:</label>
            <input type="date" name="end_date" class ="end_date" id="end_date" required>
            <br>
            <button type="submit" class="report" name="report">Скачать отчет</button>
         </form>

         <h1>Записи на прием</h1>
             <form method="get" style="margin-bottom: 20px;">
                <input type="hidden" name="tab" value="appoints">
                <label for="sort">Сортировать по:</label>
                <select name="sort" id="sort" onchange="this.form.submit()">
                   <option value="date" <?php echo ($order_by == 'date') ? 'selected' : ''; ?>>Дате</option>
                   <option value="service_name" <?php echo ($order_by == 'service_name') ? 'selected' : ''; ?>>Названию услуги</option>
                   <option value="last_name" <?php echo ($order_by == 'last_name') ? 'selected' : ''; ?>>Фамилии</option>
                   <option value="status" <?php echo ($order_by == 'status') ? 'selected' : ''; ?>>Статусу</option>
                </select>
             </form>
             <table>
                    <tr>
                       <th>Название услуги</th>
                       <th>Дата</th>
                       <th>Время</th>
                       <th>Статус</th>
                       <th>Фамилия</th>
                       <th>Имя</th>
                       <th>Отчество</th>
                       <th>Действия</th>
                    </tr>
                    <?php while ($row = $appointment->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['time']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['father_name']); ?></td>
                        <td>
                         <form method="post" style="display: inline;">
                          <input type="hidden" name="id_appointment" value="<?php echo $row['id_appointment']; ?>">
                          <input type="hidden" name="status" value="Одобрена">
                          <button type="submit" class="status" onclick="return confirm('Вы уверены, что хотите одобрить статус?');">Одобрить</button>
                         </form>
                         <form method="post" style="display: inline;">
                          <input type="hidden" name="id_appointment" value="<?php echo $row['id_appointment']; ?>">
                          <input type="hidden" name="status" value="Отменена">
                          <button type="submit" class="status" onclick="return confirm('Вы уверены, что хотите отменить статус?');">Отменить</button>
                         </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
             </table>
</div>


<div class="tab <?= $activeTab === 'services' ? 'active' : '' ?>">
    
    <h2>Добавить услугу</h2>
        <form method="POST" class="add">
            <input type="text" class="add" name="service_name" placeholder="Название услуги" required>
            <input type="number" class="add" name="price" placeholder="Цена" step="0.01" required>
            <textarea name="description" class="add" placeholder="Описание услуги" required></textarea>
            <select name="id_specialization" class="add" required>
            <option value="">Выберите специальность</option>
            <?php foreach ($specializations as $specialization): ?>
              <option value="<?php echo htmlspecialchars($specialization['id_specialization']); ?>">
                  <?php echo htmlspecialchars($specialization['specialization_name']); ?>
              </option>
            <?php endforeach; ?>
            </select>
            <button type="submit" class="add" name="add_service">Добавить услугу</button>
        </form>
        <hr>

    <h2>Список услуг</h2>
    <?php if ($message): ?>
        <div class="notification show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="this.parentElement.style.display='none';">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
       <?php endif; ?>
            <?php if (empty($services)): ?>
               <p>Нет услуг для отображения.</p>
            <?php else: ?>
            <?php foreach ($services as $service): ?>
            <div>
                 <p><strong class="serv-name"><?php echo htmlspecialchars($service['service_name']); ?></strong></p>
                 <p>Цена: <?php echo htmlspecialchars($service['price']); ?> руб.</p>
                 <p>Описание: <?php echo htmlspecialchars($service['description']); ?></p>
                 <p>Специальность: <?php echo htmlspecialchars($service['specialization_name']); ?></p>
            <form method="POST" style="display:inline;" class="edit">
                 <input type="hidden" name="id_service" value="<?php echo $service['id_service']; ?>">
                 <input type="text" name="service_name" class="edit" value="<?php echo htmlspecialchars($service['service_name']); ?>" required>
                 <input type="number" name="price" class="edit" value="<?php echo htmlspecialchars($service['price']); ?>" step="0.01" required>
                 <textarea name="description"  class="edit" required><?php echo htmlspecialchars($service['description']); ?></textarea>
                 <select name="id_specialization" class="edit" required>
                    <?php foreach ($specializations as $specialization): ?>
                        <option value="<?php echo htmlspecialchars($specialization['id_specialization']); ?>" <?php echo ($specialization['id_specialization'] == $service['id_specialization']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($specialization['specialization_name']); ?></option>
                    <?php endforeach; ?>
                 </select>
                 <button type="submit" class="edit" name="edit_service" onclick="return confirm('Вы уверены, что хотите изменить услугу?');">Редактировать</button>
                 
            </form>
            <form method="POST" style="display:inline;" class="delete">
                    <input type="hidden" name="id_service" value="<?php echo $service['id_service']; ?>">
                    <button type="submit" class="delete" name="delete_service"  onclick="return confirm('Вы уверены, что хотите удалить услугу?');">Удалить</button>
                </form>
                
            </div>
            
            <hr>
            <?php endforeach; ?>
            <?php endif; ?>


</div>
    
</body>
</html>