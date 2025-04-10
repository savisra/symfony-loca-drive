<?php

namespace App\Domain\Model;

use App\Domain\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $drivingLicenseIssueDate = null;

    public function __construct(
        string $email,
        string $password,
        string $firstname,
        string $lastname,
        DateTimeImmutable $drivingLicenseIssueDate,
        string $role = 'ROLE_CUSTOMER',
        UserPasswordHasherInterface $hasher
    ) 
    {
        $this->ensurePasswordValidity($password);
        $this->ensureRequiredFieldsAreProvided($email, $firstname, $lastname, $drivingLicenseIssueDate);
        //TODO: ensure email is unique

        $this->email = $email;
        $this->password = $hasher->hashPassword($this, $password);
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->drivingLicenseIssueDate = $drivingLicenseIssueDate;
        $this->roles[] = $role;
    }

    private function ensurePasswordValidity($password)
    {
        if (!$password) {
            throw new Exception("A password must be provided.");
        }

        if (mb_strlen($password) <= 8) {
            throw new Exception("The password must be > 8 characters long.");
        }

        $valid = preg_match('/^(?=(?:.*[A-Za-z]){4,})(?=(?:.*\d){4,}).{8,}$/', $password);
        
        if (!$valid) {
            throw new Exception("Password must contain at least 4 letters and 4 numbers.");
        }
    }

    private function ensureRequiredFieldsAreProvided($email, $firstname, $lastname, $drivingLicenseIssueDate)
    {
        if (!$email || !$firstname || !$lastname || !$drivingLicenseIssueDate) {
            throw new Exception("Email, first name, last name, driving license issue date are required fields. Passed: $email, $firstname, $lastname, $drivingLicenseIssueDate");
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getDrivingLicenseIssueDate(): ?\DateTimeImmutable
    {
        return $this->drivingLicenseIssueDate;
    }

    public function setDrivingLicenseIssueDate(\DateTimeImmutable $drivingLicenseIssueDate): static
    {
        $this->drivingLicenseIssueDate = $drivingLicenseIssueDate;

        return $this;
    }
}
