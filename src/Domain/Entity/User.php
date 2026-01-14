<?php

namespace App\Domain\Entity;

use App\Domain\Enums\Role;
use App\Domain\Exception\UserBlockedException;
use App\Domain\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    private string $password;

    #[ORM\Column(type: 'json')]
    private array $roles = [Role::USER->value];

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $failedLoginAttempts = 0;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $blocked = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $blockedAt;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private ?string $twoFactorCode = null;

    #[ORM\OneToOne(targetEntity: Dashboard::class, inversedBy: 'author', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Dashboard $dashboard = null;

    public static function create(string $email, string $hashedPassword): self
    {
        $email = strtolower(trim($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email '$email' is not valid.");
        }

        $user = new self();

        $user->email = $email;
        $user->password = $hashedPassword;

        return $user;
    }

    #[ORM\PrePersist]
    public function setDashboard(): void
    {
        $dashboard = new Dashboard();
        $dashboard->setAuthor($this);
        $this->dashboard = $dashboard;
    }

    public function recordFailedLoginAttempts(): void
    {
        if ($this->blocked) {
            throw UserBlockedException::create($this->email);
        }

        $this->failedLoginAttempts++;

        if ($this->failedLoginAttempts >= self::MAX_LOGIN_ATTEMPTS) {
            $this->blocked = true;
            $this->blockedAt = new \DateTimeImmutable();
        }
    }

    public function unblock(): void
    {
        $this->blocked = false;
        $this->blockedAt = null;
        $this->failedLoginAttempts = 0;
    }

    public function getRemainingAttempts(): int
    {
        return max(0, self::MAX_LOGIN_ATTEMPTS - $this->failedLoginAttempts);
    }

    public function getFailedLoginAttempts(): int
    {
        return $this->failedLoginAttempts;
    }

    public function isBlocked(): bool
    {
        return $this->blocked;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getDashboard(): ?Dashboard
    {
        return $this->dashboard;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = Role::USER->value;

        return array_unique($roles);
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function isEmailAuthEnabled(): bool
    {
        return true;
    }

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    public function getEmailAuthCode(): string|null
    {
        return $this->twoFactorCode;
    }

    public function setEmailAuthCode(string $authCode): void
    {
        $this->twoFactorCode = $authCode;
    }
}
