<?php

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CustomerImportSaveControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_import_customers_from_csv()
    {
        $user = $this->signIn();
        $this->actingAs($user);

        $response = $this->post('customer/import/save', []);
        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $path = str_replace('\\', DIRECTORY_SEPARATOR, base_path('tests\Feature\Controllers\Customer\hammer_customer_example_20221212.csv'));
        $file = new UploadedFile($path, 'hammer_customer_example_20221212.csv');
        $response = $this->post('customer/import/save', ['upload' => $file]);
        $response->assertRedirect('/customer');

        $customer = Customer::first();
        $this->assertEquals('John Doe Corp.', $customer->name);
        $this->assertEquals('John Doe Corp.', $customer->business_name);
        $this->assertEquals('1111111111', $customer->phone);
    }
}
