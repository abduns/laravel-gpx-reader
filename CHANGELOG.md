# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2025-12-08

### Changed
- Widened `illuminate/support` requirement to `^9.0|^10.0|^11.0` to support Laravel 9.
- Lowered PHP requirement to `^8.0`.

## [1.0.0] - 2025-12-08

### Added
- Initial release of the package.
- Core `GpxParser` for parsing GPX 1.1 files.
- DTOs for GPX elements: `GpxDocument`, `Metadata`, `Waypoint`, `Route`, `Track`, `TrackSegment`, `TrackPoint`, etc.
- Laravel integration via `GpxServiceProvider` and `Gpx` Facade.
- Configuration file `config/gpx.php` for strict mode and timezone settings.
- Comprehensive test suite using Pest.
- Documentation in `README.md`.
