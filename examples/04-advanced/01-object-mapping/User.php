<?php

/**
 * User data model class
 */
class User {
    private ?string $address = null;
    private ?int $age = null;
    private ?string $email = null;
    private ?string $name = null;
    private ?string $phone = null;

    public function getAddress(): ?string {
        return $this->address;
    }

    public function getAge(): ?int {
        return $this->age;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getPhone(): ?string {
        return $this->phone;
    }

    public function setAddress(?string $address): void {
        if ($address !== null) {
            $this->address = trim($address);
        }
    }

    public function setAge(int $age): void {
        if ($age < 0 || $age > 150) {
            throw new InvalidArgumentException('Age must be between 0 and 150');
        }
        $this->age = $age;
    }

    public function setEmail(string $email): void {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
        $this->email = strtolower($email);
    }

    public function setEmailAddress(?string $email): void {
        if ($email !== null) {
            $this->setEmail($email);
        }
    }

    // Custom setters for alternative parameter names
    public function setFullName(?string $name): void {
        if ($name !== null) {
            $this->setName($name);
        }
    }

    public function setName(string $name): void {
        $this->name = trim($name);
    }

    public function setPhone(?string $phone): void {
        if ($phone !== null) {
            $this->phone = preg_replace('/[^0-9+\-\s]/', '', $phone);
        }
    }

    public function setUserAge(?int $age): void {
        if ($age !== null) {
            $this->setAge($age);
        }
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'age' => $this->age,
            'phone' => $this->phone,
            'address' => $this->address
        ];
    }

    public function validate(): array {
        $errors = [];

        if (empty($this->name)) {
            $errors[] = 'Name is required';
        }

        if (empty($this->email)) {
            $errors[] = 'Email is required';
        }

        if ($this->age === null) {
            $errors[] = 'Age is required';
        }

        return $errors;
    }
}
