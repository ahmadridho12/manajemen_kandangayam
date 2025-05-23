@extends('layout.main')

@section('content')
<x-breadcrumb :values="[__('Monitoring Ayam'), __('Tambah Laporan')]"/>

<div class="card mb-4">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inventory.monitoring.store') }}" method="POST">
        @csrf
        <div class="card-body row">
            <input type="hidden" name="type" value="outgoing">

            {{-- Pilih Periode Ayam (menentukan jumlah skat) --}}
            <div class="col-sm-12 col-md-6">
                <label for="ayam_id" class="form-label">{{ __('Periode') }}</label>
                <select name="ayam_id" id="ayam_id" class="form-control" required>
                    @foreach($ayams as $ayam)
                        <option 
                            value="{{ $ayam->id_ayam }}" 
                            data-jumlah-skat="{{ $ayam->kandang->jumlah_skat }}">
                            {{ $ayam->periode }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tanggal Monitoring --}}
            <div class="col-sm-12 col-md-6">
                <x-input-form name="tanggal_monitoring" :label="__('Tanggal')" type="date"/>
            </div>

            {{-- Dinamis input skat_x_bw --}}
            <div id="skat-inputs" class="row mt-3"></div>

            <div class="col-12">
                <button type="submit" class="btn btn-success mt-3">Kirim</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const ayamSelect = document.getElementById('ayam_id');
    const skatInputsContainer = document.getElementById('skat-inputs');

    function generateSkatInputs(jumlahSkat) {
        skatInputsContainer.innerHTML = '';

        // // Buat header info
        // const headerDiv = document.createElement('div');
        // headerDiv.className = 'col-12 mb-2';
        // headerDiv.innerHTML = `
        //     <div class="alert alert-info">
        //         <strong>Kandang ini memiliki ${jumlahSkat} skat</strong><br>
        //         <small>Masukkan berat untuk setiap skat (minimal 1 skat harus diisi)</small>
        //     </div>
        // `;
        // skatInputsContainer.appendChild(headerDiv);

        // Generate input untuk setiap skat
        for (let i = 1; i <= jumlahSkat; i++) {
            const div = document.createElement('div');
            div.className = 'col-sm-12 col-md-6 col-lg-3 mt-2';
            div.innerHTML = `
                <label for="skat_${i}_bw" class="form-label">
                    Berat Skat ${i} <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input 
                        type="number" 
                        name="skat_${i}_bw" 
                        class="form-control skat-input" 
                        id="skat_${i}_bw" 
                        placeholder="0.00"
                        step="0.01" 
                        min="0"
                        data-skat="${i}"
                        required>
                    <span class="input-group-text">Gram</span>
                </div>
                <div class="invalid-feedback" id="error_skat_${i}"></div>
            `;
            skatInputsContainer.appendChild(div);
        }

        // Tambahkan event listener untuk validasi real-time
        addValidationListeners();
    }

    function addValidationListeners() {
        const skatInputs = document.querySelectorAll('.skat-input');
        
        skatInputs.forEach(input => {
            input.addEventListener('input', function() {
                validateSkatInput(this);
                updateSummary();
            });
        });
    }

    function validateSkatInput(input) {
        const value = parseFloat(input.value);
        const skatNum = input.dataset.skat;
        const errorDiv = document.getElementById(`error_skat_${skatNum}`);
        
        input.classList.remove('is-invalid', 'is-valid');
        errorDiv.textContent = '';

        if (input.value === '') {
            return; // Empty is allowed, but at least one must be filled
        }

        if (isNaN(value) || value < 0) {
            input.classList.add('is-invalid');
            errorDiv.textContent = 'Berat harus berupa angka positif';
            return false;
        }
        else {
            input.classList.add('is-valid');
        }

        return true;
    }

    function updateSummary() {
        const skatInputs = document.querySelectorAll('.skat-input');
        let total = 0;
        let count = 0;
        let hasValue = false;

        skatInputs.forEach(input => {
            const value = parseFloat(input.value);
            if (!isNaN(value) && value > 0) {
                total += value;
                count++;
                hasValue = true;
            }
        });

        // Update atau buat summary
        let summaryDiv = document.getElementById('weight-summary');
        if (!summaryDiv) {
            summaryDiv = document.createElement('div');
            summaryDiv.id = 'weight-summary';
            summaryDiv.className = 'col-12 mt-3';
            skatInputsContainer.appendChild(summaryDiv);
        }

        if (hasValue) {
            const average = total / count;
            summaryDiv.innerHTML = `
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Ringkasan Berat</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Total:</strong> ${total.toFixed(2)} g
                            </div>
                            <div class="col-md-4">
                                <strong>Skat Aktif:</strong> ${count} dari ${skatInputs.length}
                            </div>
                            <div class="col-md-4">
                                <strong>Rata-rata:</strong> ${average.toFixed(2)} g
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            summaryDiv.innerHTML = `
                <div class="alert alert-warning">
                    <small><i class="fas fa-exclamation-triangle"></i> Belum ada data berat yang dimasukkan</small>
                </div>
            `;
        }
    }

    function updateSkatInputs() {
        const selectedOption = ayamSelect.options[ayamSelect.selectedIndex];
        const jumlahSkat = parseInt(selectedOption.getAttribute('data-jumlah-skat')) || 4;
        generateSkatInputs(jumlahSkat);
    }

    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const skatInputs = document.querySelectorAll('.skat-input');
        let hasValidInput = false;

        skatInputs.forEach(input => {
            const value = parseFloat(input.value);
            if (!isNaN(value) && value > 0) {
                hasValidInput = true;
            }
        });

        if (!hasValidInput) {
            e.preventDefault();
            alert('Setidaknya 1 skat harus memiliki berat lebih dari 0!');
            return false;
        }
    });

    if (ayamSelect) {
        ayamSelect.addEventListener('change', updateSkatInputs);
        updateSkatInputs(); // Jalankan saat load pertama
    }
});
</script>
@endpush

@endsection
