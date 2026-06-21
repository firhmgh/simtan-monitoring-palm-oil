#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
SIMTAN - Sistem Informasi Monitoring Areal Tanaman
Skrip Analisis Validasi Rekomendasi AI menggunakan Fleiss' Kappa Statistics.
Dilengkapi dengan Visualisasi Distribusi Frekuensi & Heatmap Konsensus Pakar.
Dipakai untuk dokumentasi Bab 4 Skripsi / Pengujian Validitas Pakar.
"""

import os
import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
from statsmodels.stats.inter_rater import aggregate_raters, fleiss_kappa


def load_and_preprocess_data(file_path):
    """
    Membaca berkas CSV hasil penilaian pakar, mengabaikan header sampah,
    dan melakukan encoding jawaban tekstual menjadi numerik.
    """
    if not os.path.exists(file_path):
        raise FileNotFoundError(
            f"Berkas data tidak ditemukan pada: {file_path}")

    # Membaca CSV dengan melewati baris pertama (skiprows=1) karena berisi header sampah
    df = pd.read_csv(file_path, sep=';', skiprows=1)

    # Hapus kolom Timestamp karena tidak diperlukan untuk analisis statistik
    if 'Timestamp' in df.columns:
        df = df.drop(columns=['Timestamp'])

    # Hapus baris kosong jika ada
    df = df.dropna(how='all')

    n_raters = len(df)
    print("=" * 60)
    print(f"DATASET INFO:")
    print(f"Jumlah Pakar (Penilai) terdeteksi: {n_raters}")
    print(f"Jumlah Pertanyaan/Subjek terdeteksi: {df.shape[1]}")
    print("=" * 60)

    if n_raters < 3:
        print("PERINGATAN AKADEMIK:")
        print("Secara metodologi, Fleiss' Kappa idealnya membutuhkan minimal 3 pakar.")
        print("Pengujian dengan 2 pakar secara teknis dapat dijalankan, namun setara")
        print("dengan Cohen's Kappa. Disarankan menambah pakar untuk validasi Bab 4.")
        print("-" * 60)

    # Transpose data agar berformat: (n_subjects x n_raters)
    data_matrix = df.values.T

    # Definisikan pemetaan (Encoding) jawaban
    mapping = {
        'Sesuai': 2,
        'Cukup Sesuai': 1,
        'Tidak Sesuai': 0
    }

    # Lakukan vektorisasi fungsi pemetaan untuk mengubah string menjadi numerik
    def encode_value(val):
        if pd.isna(val):
            return np.nan
        val_str = str(val).strip()
        return mapping.get(val_str, np.nan)

    vectorized_encoder = np.vectorize(encode_value)
    encoded_matrix = vectorized_encoder(data_matrix)

    # Hitung total frekuensi pemilihan label oleh pakar untuk tabel ringkasan
    flat_data = data_matrix.flatten()
    summary_counts = {
        'Sesuai': np.sum(flat_data == 'Sesuai'),
        'Cukup Sesuai': np.sum(flat_data == 'Cukup Sesuai'),
        'Tidak Sesuai': np.sum(flat_data == 'Tidak Sesuai')
    }

    # Siapkan DataFrame untuk visualisasi Heatmap
    pakar_names = [f"Pakar {i+1}" for i in range(n_raters)]
    pertanyaan_names = [f"P{i+1}" for i in range(df.shape[1])]
    heatmap_df = pd.DataFrame(
        encoded_matrix, index=pertanyaan_names, columns=pakar_names)

    return encoded_matrix, summary_counts, n_raters, heatmap_df


def get_landis_koch_interpretation(kappa_score):
    """
    Interpretasi nilai Fleiss' Kappa berdasarkan skala Landis & Koch (1977).
    """
    if kappa_score < 0:
        return "Poor Agreement (Kesepakatan Sangat Buruk)"
    elif kappa_score <= 0.20:
        return "Slight Agreement (Kesepakatan Sangat Rendah)"
    elif kappa_score <= 0.40:
        return "Fair Agreement (Kesepakatan Cukup)"
    elif kappa_score <= 0.60:
        return "Moderate Agreement (Kesepakatan Sedang)"
    elif kappa_score <= 0.80:
        return "Substantial Agreement (Kesepakatan Kuat)"
    else:
        return "Almost Perfect Agreement (Kesepakatan Hampir Sempurna)"


def create_visualizations(summary_counts, heatmap_df, output_image_path):
    """
    Membuat visualisasi Bar Chart Distribusi dan Heatmap Konsensus Pakar,
    kemudian menyimpannya sebagai berkas gambar PNG.
    """
    # Atur tema visualisasi menggunakan Seaborn & Matplotlib
    sns.set_theme(style="whitegrid")
    plt.rcParams['font.family'] = 'sans-serif'
    plt.rcParams['font.sans-serif'] = ['Plus Jakarta Sans',
                                       'DejaVu Sans', 'Arial']

    # Buat figure dengan 2 kolom subplot berdampingan
    fig, axes = plt.subplots(1, 2, figsize=(15, 6), gridspec_kw={
                             'width_ratios': [1.2, 1]})

    # -------------------------------------------------------------------------
    # SUBPLOT 1: BAR CHART DISTRIBUSI PILIHAN PAKAR
    # -------------------------------------------------------------------------
    labels = list(summary_counts.keys())
    values = list(summary_counts.values())

    # Palette warna Vristo: Emerald (Sesuai), Amber (Cukup Sesuai), Rose (Tidak Sesuai)
    colors = ['#10B981', '#F59E0B', '#F43F5E']

    bars = axes[0].bar(labels, values, color=colors,
                       edgecolor='none', width=0.6, zorder=3)
    axes[0].set_title("Distribusi Frekuensi Penilaian Rekomendasi AI oleh Pakar",
                      fontsize=12, fontweight='bold', pad=15)
    axes[0].set_xlabel("Kategori Kesesuaian", fontsize=10,
                       fontweight='bold', labelpad=10)
    axes[0].set_ylabel("Frekuensi Pilihan (Kali)",
                       fontsize=10, fontweight='bold', labelpad=10)
    axes[0].set_ylim(0, max(values) + 3 if max(values) > 0 else 10)
    axes[0].grid(axis='y', linestyle='--', alpha=0.7, zorder=0)

    # Tambahkan label angka di atas setiap bar
    for bar in bars:
        height = bar.get_height()
        axes[0].annotate(f'{int(height)}',
                         xy=(bar.get_x() + bar.get_width() / 2, height),
                         xytext=(0, 5),  # offset vertikal ke atas
                         textcoords="offset points",
                         ha='center', va='bottom', fontsize=10, fontweight='bold')

    # -------------------------------------------------------------------------
    # SUBPLOT 2: HEATMAP PENILAIAN PAKAR PER PERTANYAAN
    # -------------------------------------------------------------------------
    # Custom colormap untuk nilai: 0 (Rose/Tidak Sesuai), 1 (Amber/Cukup), 2 (Emerald/Sesuai)
    custom_cmap = sns.color_palette(['#F43F5E', '#F59E0B', '#10B981'])

    # Gambar Heatmap
    sns.heatmap(heatmap_df, annot=True, fmt=".0f", cmap=custom_cmap, cbar=False,
                linewidths=1.5, linecolor='white', ax=axes[1],
                annot_kws={"size": 10, "weight": "bold", "color": "white"})

    axes[1].set_title("Peta Konsensus Penilaian Pakar per Pertanyaan",
                      fontsize=12, fontweight='bold', pad=15)
    axes[1].set_xlabel("Penilai (Raters)", fontsize=10,
                       fontweight='bold', labelpad=10)
    axes[1].set_ylabel("Subjek Uji (Pertanyaan)", fontsize=10,
                       fontweight='bold', labelpad=10)

    # Sesuaikan layout agar tidak ada teks terpotong
    plt.tight_layout()

    # Simpan grafik secara fisik ke disk
    plt.savefig(output_image_path, dpi=300, bbox_inches='tight')
    plt.close()
    print(f"GRAFIK BERHASIL DISIMPAN: {output_image_path}")
    print("=" * 60)


def print_hci_interpretation(summary_counts, kappa_score, interpretation):
    """
    Mencetak panduan penjelasan teknis grafik sesuai prinsip HCI (Human-Computer Interaction)
    untuk kebutuhan Bab 4 skripsi.
    """
    print("\nPANDUAN DOKUMENTASI AKADEMIK (HCI INTERPRETATION) - BAB 4:")
    print("-" * 60)
    print("1. Cara Membaca Bar Chart (Distribusi Frekuensi):")
    print("   - Grafik batang kiri menggambarkan penyebaran keputusan yang diambil oleh para pakar")
    print("     terhadap rekomendasi sistem monitoring kelapa sawit SIMTAN.")
    print("   - Batang Hijau (Sesuai) menunjukkan tingkat penerimaan sistem tinggi, sedangkan")
    print("     Kuning (Cukup Sesuai) dan Merah (Tidak Sesuai) menandakan area yang membutuhkan perbaikan.")
    print(
        f"   - Dari total penilaian, sistem mendapatkan respon 'Sesuai' sebanyak {summary_counts['Sesuai']} kali")
    print(
        f"     dan 'Cukup Sesuai' sebanyak {summary_counts['Cukup Sesuai']} kali.")
    print("")
    print("2. Cara Membaca Heatmap (Konsensus Penilaian):")
    print("   - Heatmap di sebelah kanan menunjukkan tingkat konsistensi penilaian antar pakar per pertanyaan.")
    print("   - Warna Hijau Solid (Nilai 2) di seluruh kolom menandakan konsensus sempurna bahwa AI bekerja")
    print("     sesuai standar. Sel warna Kuning (Nilai 1) atau Merah (Nilai 0) menandakan ketidaksepakatan")
    print("     atau anomali pada pertanyaan/aspek tertentu yang perlu dikaji ulang.")
    print("")
    print("3. Analisis Hasil Statistik:")
    print(
        f"   - Nilai Fleiss' Kappa berada di angka {kappa_score:.4f} ({interpretation}).")
    print("   - Nilai ini merupakan ukuran obyektif bebas bias untuk menyatakan sejauh mana sistem")
    print("     direkomendasikan secara kolektif oleh para pakar di lapangan.")
    print("=" * 60)


def main():
    file_path = "research/dataset_pakar.csv"
    output_image = "research/hasil_validasi.png"

    try:
        # 1. Preprocessing Data
        encoded_data, summary_counts, n_raters, heatmap_df = load_and_preprocess_data(
            file_path)

        # 2. Ringkasan Frekuensi Distribusi Jawaban
        print("RINGKASAN DISTRIBUSI PILIHAN PAKAR:")
        total_pilihan = sum(summary_counts.values())
        for label, count in summary_counts.items():
            percentage = (count / total_pilihan) * \
                100 if total_pilihan > 0 else 0
            print(f"- {label:<15}: {count:>3} kali ({percentage:>5.1f}%)")
        print("-" * 60)

        # 3. Agregasi rating ke format frekuensi kategori per subjek
        agg_data, categories = aggregate_raters(encoded_data)

        # 4. Kalkulasi Fleiss' Kappa Score
        kappa_score = fleiss_kappa(agg_data)
        interpretation = get_landis_koch_interpretation(kappa_score)

        # 5. Output Hasil Statistik
        print("HASIL EVALUASI STATISTIK:")
        print(f"Fleiss' Kappa Score : {kappa_score:.4f}")
        print(f"Interpretasi        : {interpretation}")
        print("=" * 60)

        # 6. Generate & Save Visualizations
        create_visualizations(summary_counts, heatmap_df, output_image)

        # 7. Print HCI Interpretation Guide
        print_hci_interpretation(summary_counts, kappa_score, interpretation)

    except Exception as e:
        print(f"Terjadi kesalahan saat memproses data: {e}")


if __name__ == "__main__":
    main()
