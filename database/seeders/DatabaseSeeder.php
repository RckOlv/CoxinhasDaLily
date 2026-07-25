<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Category::query()->forceDelete();

        $categories = [
            [
                "name" => "Congelados",
                "products" => [
                    ["name" => "Coxinha de Pollo", "description" => "Bolsa 1kg - Aprox 25 unidades. Croqueta de masa rellena de pollo.", "price" => 5800, "stock_quantity" => 15, "image_path" => "/img/coxinhas.webp"],
                    ["name" => "Coxinha de Palmito", "description" => "Bolsa 1kg - Vegetariana con palmito y queso.", "price" => 6200, "stock_quantity" => 0, "image_path" => null],
                    ["name" => "Pastel de Carne", "description" => "Bolsa 1kg - Masa hojaldrada rellena de carne sazonada.", "price" => 6000, "stock_quantity" => 12, "image_path" => null],
                    ["name" => "Pao de Queijo", "description" => "Bolsa 1kg - Panecillos de queso, aprox 24 unidades.", "price" => 6500, "stock_quantity" => 20, "image_path" => null],
                    ["name" => "Empanada de Carne x20", "description" => "Bolsa 2kg - Empanadas de carne cortada a cuchillo.", "price" => 8400, "stock_quantity" => 0, "image_path" => null],
                    ["name" => "Esfirra de Carne", "description" => "Bolsa 1kg - Pan relleno estilo arabe con carne especiada.", "price" => 6800, "stock_quantity" => 8, "image_path" => null],
                    ["name" => "Mini Pizzas", "description" => "Bolsa 1kg - Mini pizzas rellenas de jamón y queso. Aprox 20 unidades.", "price" => 7200, "stock_quantity" => 10, "image_path" => "/img/minipizza.webp"],
                    ["name" => "Churros", "description" => "Bolsa 1kg - Churros rellenos de dulce de leche. Aprox 15 unidades.", "price" => 5500, "stock_quantity" => 18, "image_path" => "/img/churros.webp"],
                    ["name" => "Mini Churros", "description" => "Bolsa 500g - Mini churros para picar. Aprox 40 unidades.", "price" => 4200, "stock_quantity" => 25, "image_path" => "/img/minichurros.webp"],
                ],
            ],
            [
                "name" => "Bandejas para Eventos",
                "products" => [
                    ["name" => "Bandeja Executiva", "description" => "Para 10 personas. Coxinha, pastel, pao de queijo y esfirra.", "price" => 28000, "stock_quantity" => 5, "image_path" => null],
                    ["name" => "Bandeja Festa", "description" => "Para 20 personas. Mix completo de salgados.", "price" => 52000, "stock_quantity" => 3, "image_path" => null],
                    ["name" => "Bandeja Combo", "description" => "Para 15 personas. Coxinha + Pao de Queijo + Bebidas.", "price" => 45000, "stock_quantity" => 0, "image_path" => null],
                    ["name" => "Degustacion Brasilera", "description" => "Para 8 personas. Seleccion premium.", "price" => 22000, "stock_quantity" => 4, "image_path" => null],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $products = $catData["products"];
            unset($catData["products"]);
            $category = Category::create($catData);
            foreach ($products as $productData) {
                $category->products()->create($productData);
            }
        }
    }
}
