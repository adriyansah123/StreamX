<nav class="navbar navbar-expand-lg curved-navbar fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">StreamX</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- Movie Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Movie
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php">List</a></li>
                        <li><a class="dropdown-item" href="../search.php?jenis=genre&type=movie">Genre</a></li>
                        <li><a class="dropdown-item" href="../search.php?jenis=pemeran&type=movie">Actor</a></li>
                        <li><a class="dropdown-item" href="../search.php?jenis=tahun&type=movie">Year</a></li>
                    </ul>
                </li>
                <!-- Music Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Music
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="music.php">List</a></li>
                        <li><a class="dropdown-item" href="../search.php?jenis=genre&type=music">Genre</a></li>
                        <li><a class="dropdown-item" href="../search.php?jenis=artist&type=music">Artist</a></li>
                        <li><a class="dropdown-item" href="../search.php?jenis=tahun&type=music">Year</a></li>
                    </ul>
                </li>
                <!-- Comics Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Comics
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="comic.php">List</a></li>
                        <li><a class="dropdown-item" href="../search.php?jenis=genre&type=comics">Genre</a></li>
                        <li><a class="dropdown-item" href="../search.php?jenis=artist&type=comics">Artist</a></li>
                        <li><a class="dropdown-item" href="../search.php?jenis=publisher&type=comics">Publisher</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Search Bar -->
            <form id="formpencarian" class="d-flex" role="search" method="GET">
                <input id="datakeyword" class="form-control me-2" type="search" name="keyword" placeholder="Search"
                    aria-label="Search">
                <select class="form-select me-2" name="type" id="datatype">
                    <option value="index.php">Movie</option>
                    <option value="music.php">Music</option>
                    <option value="comic.php">Comics</option>
                </select>
                <button class="btn btn-outline-light" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>