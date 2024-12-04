<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-gray-300 font-family:Figtree">
    <div>
        <h1>{{ $contents->personal->name }}</h1>
        <h3>{{ $contents->personal->email }}</h3>
        @if(!empty($contents->personal->linkedin))
            <p>{{ $contents->personal->linkedin }}</p>
        @endif
        <p>{{ $contents->personal->summary }}</p>
    </div>

    <div>
        <h2>Experience</h2>
        @foreach($contents->experiences as $experience)
            <h3>{{ $experience->position }}</h3>
            <p>{{ $experience->company }}</p>
            <p>{{ $experience->start_date }} - {{ $experience->end_date }}</p>
            <p>{{ $experience->description }}</p>
        @endforeach
    </div>

    @if(!empty($contents->educations))
        <div>
            <h2>Education</h2>
            @foreach($contents->educations as $education)
                <h3>{{ $education->degree }}</h3>
                <p>{{ $education->institution }}</p>
                <p>{{ $education->start_date }} - {{ $education->end_date }}</p>
            @endforeach
        </div>
   @endif
</body>
</html>
