<?php

class ExamDao
{

  private $conn;

  /**
   * constructor of dao class
   */
  public function __construct()
  {
    try {
      /** TODO
       * List parameters such as servername, username, password, schema. Make sure to use appropriate port
       */
      $servername = "127.0.0.1";
      $username = "root";
      $password = "rootroot";
      $schema = "classicmodels";
      $port = 3306;


      /** TODO
       * Create new connection
       */
      $this->conn = new PDO(
        "mysql:host=" . $servername . ";dbname=" . $schema . ";port=" . $port,
        $username,
        $password,
        [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
      );
      //echo "Connected successfully";
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
  }

  /** TODO
   * Implement DAO method used to get employees performance report
   */

  /** TODO
   * This endpoint returns performance report for every employee.
   * It should return array of all employees where every element
   * in array should have following properties
   *   `id` -> employeeNumber of the employee
   *   `full_name` -> concatenated firstName and lastName of the employee
   *   `total` -> total amount of money earned for every employee.
   *              aggregated amount from payments table for every employee
   * This endpoint should return output in JSON format
   * 10 points
   */
  public function employees_performance_report()
  {
    $query = "SELECT e.employeeNumber as id, CONCAT(firstName, ' ', lastName) as full_name, SUM(p.amount) AS total
    FROM employees e
    JOIN customers c ON c.salesRepEmployeeNumber = e.employeeNumber
    JOIN payments p ON p.customerNumber = p.customerNumber
    GROUP BY e.employeeNumber";
    $params = [];
    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /** TODO
   * Implement DAO method used to delete employee by id
   */

  /** TODO
   * This endpoint should delete the employee from database with provided id.
   * This endpoint should return output in JSON format that contains only 
   * `message` property that indicates that process went successfully.
   * 5 points
   */
  public function delete_employee($employee_id)
  {
    $query = "
    SET FOREIGN_KEY_CHECKS = 0;
    DELETE FROM employees WHERE employeeNumber = :employee_id;
    SET FOREIGN_KEY_CHECKS = 1;";
    $params = ["employee_id" => $employee_id];
    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /** TODO
   * Implement DAO method used to edit employee data
   */
  /** TODO
   * This endpoint should save edited employee to the database.
   * The data that will come from the form (if you don't change
   * the template form) has following properties
   *   `first_name` -> first name of the employee
   *   `last_name` -> last name of the employee
   *   `email` -> email of the employee
   * This endpoint should return the edited customer in JSON format
   * 10 points
   */
  public function edit_employee($employee_id, $data)
  {
    // First, update the employee
    $query = "UPDATE employees 
              SET firstName = :first_name, 
                  lastName = :last_name, 
                  email = :email 
              WHERE employeeNumber = :employee_id";

    $params = [
      "first_name" => $data["first_name"],
      "last_name" => $data["last_name"],
      "email" => $data["email"],
      "employee_id" => $employee_id
    ];

    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);

    // Then, fetch and return the updated employee
    $query = "SELECT employeeNumber AS id, firstName, lastName, email 
              FROM employees 
              WHERE employeeNumber = :employee_id";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /** TODO
   * Implement DAO method used to get orders report
   */
  /** TODO
   * This endpoint should return the report for every order in the database.
   * For every order we need the amount of money spent for the order. In order
   * to get total money for every order quantityOrdered should be multiplied 
   * with priceEach from the orderdetails table. The data should be summarized
   * in order to get accurate report. paginated. Every item returned should 
   * have following properties:
   *   `details` -> the html code needed on the frontend. Refer to `orders.html` page
   *   `order_number` -> orderNumber of the order
   *   `total_amount` -> aggregated amount of money spent per order
   * This endpoint should return output in JSON format
   * 10 points
   */
  public function get_orders_report($offset, $limit)
  {
    $query = "SELECT o.orderNumber as order_number, SUM(od.quantityOrdered*od.priceEach) AS total_amount
    FROM orders o
    JOIN orderdetails od ON od.orderNumber = o.orderNumber
    GROUP BY o.orderNumber
    LIMIT {$offset}, {$limit}";
    $params = [];
    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /** TODO
   * Implement DAO method used to get all products in a single order
   */
  /** TODO
   * This endpoint should return the array of all products in a single 
   * order with the provided id. Every food returned should have 
   * following properties:
   *   `product_name` -> productName from the database
   *   `quantity` -> quantity from the orderdetails table
   *   `price_each` -> priceEach from the orderdetails table
   * This endpoint should return output in JSON format
   * 10 points
   */
  public function get_order_details($order_id, $offset, $limit)
  {
    $query = "SELECT o.orderNumber, p.productName as product_name, od.quantityOrdered AS quantity, od.priceEach as price_each
    FROM products p
    JOIN orderdetails od ON p.productCode = od.productCode
    JOIN orders o ON o.orderNumber = od.orderNumber
    WHERE od.orderNumber = :order_id
    LIMIT {$offset}, {$limit}";
    $params = ["order_id" => $order_id];
    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
