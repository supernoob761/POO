<?php
class CustomerRepository{
private PDO $pdo;

public function __construct(){

    $this->pdo = Db::getInstance()->connection();
}

public function showAll(){
    $stmt = $this->pdo->query("SELECT id, name, email FROM customers");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function AddCus(string $name, string $email){
 $sql = "INSERT INTO customers (name,email) VALUES (:name,:email)";
 $stmt = $this->pdo->prepare($sql);

 return $stmt->execute([
'name' => $name,
'email' => $email

 ]);
}  
public function Update(string $name, string $email, int $id){
  $sql = "UPDATE customers 
        SET name = :name, email = :email 
        WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id'    => $id,
            'name'  => $name,
            'email' => $email
        ]);
}

public function Delete(int $id){
$stmt = $this->pdo->prepare("DELETE FROM customers WHERE id = :id");
return $stmt->execute(['id' => $id]);
}
public function findClient(string $name)
    { echo "start";
        $stmt = $this->pdo->prepare("SELECT * FROM customers WHERE name = :name");
        $stmt->execute(['name' => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
        echo "finished";
    }
}


?>