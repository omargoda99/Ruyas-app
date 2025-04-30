<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for each certification
            $table->foreignId('interpreter_id')->constrained('interpreters')->onDelete('cascade'); // Foreign key to interpreters table
            $table->string('name'); // Certification name
            $table->string('issuing_organization'); // Issuing organization
            $table->date('issue_date'); // Issue date
            $table->string('credential_id')->nullable(); // Credential ID (optional)
            $table->string('credential_url')->nullable(); // Credential URL (optional)
            $table->string('credential_img');
            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
