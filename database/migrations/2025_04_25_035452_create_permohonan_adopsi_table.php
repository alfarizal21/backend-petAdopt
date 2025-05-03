<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permohonan_adopsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('hewan_id')->constrained('hewan')->onDelete('cascade');
            $table->string('nama');
            $table->integer('umur');
            $table->string('no_hp');
            $table->string('email');
            $table->string('nik');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $table->string('tempat_tanggal_lahir');
            $table->string('pekerjaan');
            $table->text('alamat');
            $table->text('riwayat_adopsi')->nullable();
            $table->enum('status', ['diterima', 'menunggu', 'ditolak'])->default('menunggu');
            $table->date('tanggal_permohonan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_adopsi');
    }
};
