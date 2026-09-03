<?php

class Review
{
    private PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }


    /* =========================================
       GET ALL REVIEWS
    ========================================= */

    public function getAll(): array
    {
        $query = "
            SELECT
                r.id,
                r.customer_id,
                r.rating,
                r.comment,
                r.status,
                r.created_at,
                c.full_name,
                c.email
            FROM reviews r
            INNER JOIN customers c
                ON c.id = r.customer_id
            ORDER BY r.id DESC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    /* =========================================
       GET APPROVED REVIEWS
    ========================================= */

    public function getApproved(): array
    {
        $query = "
            SELECT
                r.id,
                r.rating,
                r.comment,
                r.created_at,
                c.full_name
            FROM reviews r
            INNER JOIN customers c
                ON c.id = r.customer_id
            WHERE r.status = 'Approved'
            ORDER BY r.id DESC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    /* =========================================
       CREATE REVIEW
    ========================================= */

    public function create(
        int $customer_id,
        int $rating,
        string $comment
    ): bool {

        $query = "
            INSERT INTO reviews
            (
                customer_id,
                rating,
                comment,
                status
            )
            VALUES
            (
                :customer_id,
                :rating,
                :comment,
                'Pending'
            )
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(
            ":customer_id",
            $customer_id,
            PDO::PARAM_INT
        );

        $stmt->bindParam(
            ":rating",
            $rating,
            PDO::PARAM_INT
        );

        $stmt->bindParam(
            ":comment",
            $comment
        );

        return $stmt->execute();
    }


    /* =========================================
       UPDATE STATUS
    ========================================= */

    public function updateStatus(
        int $review_id,
        string $status
    ): bool {

        $query = "
            UPDATE reviews
            SET status = :status
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(
            ":status",
            $status
        );

        $stmt->bindParam(
            ":id",
            $review_id,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }


    /* =========================================
       DELETE REVIEW
    ========================================= */

    public function delete(int $review_id): bool
    {
        $query = "
            DELETE FROM reviews
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(
            ":id",
            $review_id,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }
}