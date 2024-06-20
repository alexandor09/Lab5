<?php
require '../DB/ProductEntity.php';

class DBClass
{
    static bool $isConnected = false;
    private static ?PDO $conn = null;

    public static function connect(): void
    {
        require 'DbConfig.php';
        $dsn = "pgsql:host=$host;port=5432;dbname=$db;";
        try {
            self::$conn = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            self::$isConnected = true;
        } catch (PDOException $e) {
            self::$isConnected = false;
            die($e->getMessage());
        }
    }

    public static function getUserList()
    {
        $sql = "SELECT * FROM public.user";
        $statement = self::$conn->query($sql);
        while ($row = $statement->fetch()) {
            print_r($row);
        }
    }

    /**
     * @throws UserNotExist
     */
    public static function setUserToCookie(string $name, string $password): bool
    {
        try {
            $sql = 'SELECT "user".id,"user".name,surname,email,role.name as user_role
                    from public."user", public.role 
                    where "user".name = :username and password = :password and role.id = "user".role_id';

            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(":username", $name);
            $stmt->bindValue(":password", $password);
            $stmt->execute();
            require '../utils/Cookie.php';
            if ($stmt->rowCount() > 0 and $stmt->rowCount() < 2) {
                $user = $stmt->fetch();
                Cookie::setCookies(
                    $user['id'],
                    $user['name'],
                    $user['surname'],
                    $user['email'],
                    $user['user_role']
                );
                return true;
            } else {
                throw new UserNotExist($name);
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return false;
        }
    }

    public static function registerNewUser($name, $surname, $password, $email): bool
    {
        try {
            $sql = "INSERT INTO public.user (name, surname, role_id, password, email) 
                    VALUES (:name, :surname, (select id from role where role.name = 'customer'),:password,:email)";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(":name", $name);
            $stmt->bindValue(":surname", $surname);
            $stmt->bindValue(":password", $password);
            $stmt->bindValue(":email", $email);
            if ($stmt->execute()) {
                return self::setUserToCookie($name, $password);
            } else {
                return false;
            }
        } catch (PDOException|UserNotExist $e) {

            $message = $e->getMessage();
            $message = trim(preg_replace('/\s+/', ' ', $message));

            echo "<script> console.log(" . "'" . $message . "'" . ")</script>";
            return false;
        }
    }

    public static function getProductTypeList(): array
    {
        try {
            $sql = "SELECT * from product_type";
            $stmt = self::$conn->prepare($sql);
            $stmt->execute();
            $allProdTypes = $stmt->fetchAll();
            if ($allProdTypes)
                return $allProdTypes;
            return array();
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            die();
        }
    }

    /**
     * @return ProductEntity[]
     */
    public static function getProductList(): array
    {
        try {
            $sql = "
            SELECT 
                product.id,
                product.name,
                product_type.name as product_type,
                for_women,
                description,
                price
            FROM product, product_type
            WHERE product_type_id = product_type.id";
            $statement = self::$conn->query($sql);
            $allProducts = $statement->fetchAll();
            return self::transformArray($allProducts);
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            die();
        }
    }

    public static function getProductListByFilter(string $product_type): array
    {
        try {
            $sql = "
            SELECT 
                product.id,
                product.name,
                product_type.name as product_type,
                for_women,
                description,
                price
            FROM product, product_type
            WHERE product_type_id = product_type.id AND product_type.name = :product_type";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue('product_type', $product_type);
            $stmt->execute();
            $allProducts = $stmt->fetchAll();
            return self::transformArray($allProducts);
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            die();
        }
    }

    /**
     * @param int[] $arrayOfIds
     * @return ProductEntity[]
     */
    public static function getProductByIds(array $arrayOfIds): array
    {
        try {
            $sql = "
            SELECT 
                product.id,
                product.name,
                product_type.name as product_type,
                for_women,
                description,
                price
            FROM product, product_type
            WHERE product_type_id = product_type.id AND product.id IN (" . implode(',', array_map('intval', $arrayOfIds)) . ')';
            $statement = self::$conn->query($sql);
            $allProducts = $statement->fetchAll();

            return self::transformArray($allProducts);

        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            die();
        }
    }

    /**
     * @param string $id
     * @return bool
     */
    public static function removeProductById(string $id): bool
    {
        try {
            $sql = "
            DELETE FROM product
            WHERE id = :id
            ";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue('id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return false;
        }
    }

    public static function createOrder(): bool
    {
        try {
            $dt = new DateTime("now");
            $timestamp = $dt->format('Y-m-d H:i:s P');
            if (!isset($_COOKIE['id'])) {
                return false;
            }
            if (!isset($_SESSION['id_list']) || count($_SESSION['id_list']) === 0) {
                return false;
            }

            $id = $_COOKIE['id'];
            $sql = "
            INSERT INTO public.order (create_date,approved,confirmed,customer_id)
            VALUES
            (:timestamp,false,false,:id);
            ";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue('id', $id);
            $stmt->bindValue('timestamp', $timestamp);
            if ($stmt->execute()) {
                $lastId = self::$conn->lastInsertId();

                $sql = "
                INSERT INTO public.order_info (order_id, product_id, amount)
                VALUES ";

                foreach ($_SESSION['id_list'] as $key => $amount) {
                    $sql .= "(" . $lastId . "," . $key . "," . $amount . "),";
                }
                $sql = substr($sql, 0, -1);
                self::$conn->query($sql);
                $_SESSION['id_list'] = array();
                return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            return false;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * @param array $allProducts
     * @return ProductEntity[]
     */
    private static function transformArray(array $allProducts): array
    {
        if ($allProducts) {
            $buffer = array();
            foreach ($allProducts as $prod) {
                try {
                    $buffer[] = ProductEntity::objectToEntity($prod);
                } catch (NotFound $e) {

                }
            }
            return $buffer;
        } else {
            return array();
        }
    }
}