<?php

/*
 * This file is part of the GrandoGo project.
 *
 * (c) Yesser Bkhouch <yesserbakhouch@hotmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for vehicle data during registration.
 */
class VehicleDTO
{
    #[Assert\NotBlank(message: "Vehicle make is required")]
    public string $make;

    #[Assert\NotBlank(message: "Vehicle model is required")]
    public string $model;

    #[Assert\NotBlank(message: "Vehicle year is required")]
    #[Assert\Regex(pattern: '/^\d{4}$/', message: "Year must be a 4-digit number")]
    public string $year;

    public ?string $color = null;

    #[Assert\NotBlank(message: "Vehicle type is required")]
    #[Assert\Choice(choices: ['sedan', 'suv', 'truck', 'van', 'hybrid', 'electric'], message: "Invalid vehicle type")]
    public string $type;

    #[Assert\NotBlank(message: "License plate is required")]
    public string $licensePlate;

    public ?int $vehicleTypeId = null;
}