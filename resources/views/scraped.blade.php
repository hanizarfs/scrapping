<!DOCTYPE html>
<html>
<head>
    <title>Scraped Data</title>
</head>
<body>
    <h1>Scraped Data</h1>

    <!-- Form untuk menginput ID User -->
    <form action="{{ route('scrape') }}" method="GET">
        <label for="id_user">Input ID User</label>
        <input type="text" name="id_user" id="id_user" placeholder="Masukkan ID User" required>
        <button type="submit">Scrape</button>
    </form>

    <!-- Menampilkan data profil -->
    @if(isset($dataScrapping['profile']))
        <h2>Profile Information:</h2>
        <p>Name: {{ $dataScrapping['profile']['name'] }}</p>
        <p>Affiliation: {{ $dataScrapping['profile']['affiliation'] }}</p>
        <p>Email Verified: {{ $dataScrapping['profile']['email_verified'] }}</p>
        <p>Interests: {{ $dataScrapping['profile']['interests'] }}</p>
        <p>Photo: <img src="{{ $dataScrapping['profile']['photo_url'] }}" alt="Profile Photo"></p>
    @endif

    <!-- Menampilkan data "Cited by" -->
    @if(isset($dataScrapping['cited_by']))
        <h2>Cited By Information:</h2>
        <ul>
            <li>Citations (All): {{ $dataScrapping['cited_by']['citations_all'] }}</li>
            <li>Citations (Since 2019): {{ $dataScrapping['cited_by']['citations_since_2019'] }}</li>
            <li>h-index (All): {{ $dataScrapping['cited_by']['h_index_all'] }}</li>
            <li>h-index (Since 2019): {{ $dataScrapping['cited_by']['h_index_since_2019'] }}</li>
            <li>i10-index (All): {{ $dataScrapping['cited_by']['i10_index_all'] }}</li>
            <li>i10-index (Since 2019): {{ $dataScrapping['cited_by']['i10_index_since_2019'] }}</li>
        </ul>
    @endif

    <!-- Menampilkan data grafik -->
    @if(isset($dataScrapping['chart']))
        <h2>Article Count Over Years:</h2>
        <ul>
            @foreach($dataScrapping['chart'] as $item)
                <li>Year: {{ $item['year'] }} - Count: {{ $item['count'] }}</li>
            @endforeach
        </ul>
    @endif
</body>
</html>
