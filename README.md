# VeloViewer Explorer Overlay tile-server
Generic overlay for VeloViewer Explorer squares

## Public server
  I set up open public server with this service, check at https://vv.fork.pl/
  
## Explorer info:
- https://blog.veloviewer.com/veloviewer-explorer-score-and-max-square/
- https://rideeverytile.com/
- https://www.strava.com/clubs/279168
- https://www.statshunters.com/faq-10-what-are-explorer-tiles
- https://rowerowyrownik.cf/mapa/
- https://www.wykop.pl/tag/kwadraty/
- https://squadrats.com/

## BRouter example
![brouter with overlay](brouter-example.png "brouter with overlay!")

## TODO
- [x] zoom level < 14
- [ ] color setup support (maybe including transparency option), note: 4 colors (bg, frame, max sq. frame, cluster bg)
- [x] frames
- [ ] auto-refresh of squares cache, with proper locking
- [ ] cache (at least for z=14+ and later for z=11..13)
- [x] largest square support (clashes with generic cache)
- [x] different color for clusters ("inside" squares), (note: cache)
- [ ] display boundaries of smaller squares (squadratinhos style, 8x8+)

