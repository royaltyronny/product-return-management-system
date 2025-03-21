<?php

namespace App\Console\Commands;

use App\Models\ReturnRequest;
use App\Models\ReturnShipment;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class GenerateSampleShippingLabels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'returns:generate-labels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sample shipping labels for all return requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create the shipping-labels directory if it doesn't exist
        $directory = storage_path('app/public/shipping-labels');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Get all return requests that have shipments
        $returnShipments = ReturnShipment::with(['returnRequest', 'destinationWarehouse'])->get();
        
        if ($returnShipments->isEmpty()) {
            $this->info('No return shipments found.');
            return;
        }

        $this->info('Generating sample shipping labels...');
        $bar = $this->output->createProgressBar($returnShipments->count());
        
        foreach ($returnShipments as $shipment) {
            $returnRequest = $shipment->returnRequest;
            $warehouse = $shipment->destinationWarehouse;
            $user = User::find($returnRequest->user_id);
            
            if (!$returnRequest || !$warehouse || !$user) {
                $this->warn("Missing data for shipment ID: {$shipment->id}");
                continue;
            }
            
            // Generate a sample tracking number if not already set
            if (!$shipment->tracking_number) {
                $shipment->tracking_number = 'TRK' . strtoupper(substr(md5(rand()), 0, 10));
                $shipment->save();
            }
            
            // Prepare data for the shipping label template
            $data = [
                'rma_number' => $returnRequest->rma_number,
                'customer_name' => $user->name,
                'customer_address' => '123 Customer Street',
                'customer_city' => 'Customer City',
                'customer_state' => 'CS',
                'customer_zip' => '12345',
                'customer_country' => 'USA',
                'warehouse_name' => $warehouse->name,
                'warehouse_address' => $warehouse->address ?? '456 Warehouse Avenue',
                'warehouse_city' => $warehouse->city ?? 'Warehouse City',
                'warehouse_state' => $warehouse->state ?? 'WS',
                'warehouse_zip' => $warehouse->zip_code ?? '67890',
                'warehouse_country' => $warehouse->country ?? 'USA',
                'tracking_number' => $shipment->tracking_number,
                'carrier' => $shipment->shipping_carrier ?? 'Default Carrier',
            ];
            
            // Generate HTML content from the template
            $html = View::make('shipping-label-template', $data)->render();
            
            // Save the HTML as a file (in a real app, you'd convert to PDF)
            $filename = 'rma-' . $returnRequest->rma_number . '.html';
            File::put($directory . '/' . $filename, $html);
            
            // Also create a PDF file (simulated by copying the HTML file with a PDF extension)
            $pdfFilename = 'rma-' . $returnRequest->rma_number . '.pdf';
            File::put($directory . '/' . $pdfFilename, $html);
            
            // Update the shipping label URL if needed
            if ($shipment->shipping_label_url !== 'shipping-labels/' . $pdfFilename) {
                $shipment->shipping_label_url = 'shipping-labels/' . $pdfFilename;
                $shipment->save();
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Sample shipping labels generated successfully!');
    }
}
