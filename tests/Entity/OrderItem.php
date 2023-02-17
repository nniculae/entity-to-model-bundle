<?php

declare(strict_types=1);

/*
 * This file is part of the Aristonet EntityToModelBundle package.
 *
 * @author Niculae Niculae
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aristonet\EntityToModelBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $parentOrder = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getParentOrder(): ?Order
    {
        return $this->parentOrder;
    }

    public function setParentOrder(?Order $parentOrder): self
    {
        $this->parentOrder = $parentOrder;

        return $this;
    }
}
