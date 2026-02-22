# Giscana - Geographic Information System for Natural Disaster Mitigation

A web-based Geographic Information System (GIS) designed to support disaster preparedness and response efforts in the Bone Coastal Area, Bone Bolango Regency, Gorontalo Province, Indonesia.

## Overview

Giscana is a comprehensive disaster management system that focuses on natural disasters such as banjirs and longsors. The system provides interactive maps, evacuation planning, and aid distribution management to enhance community resilience.

## Features

### 🗺️ Interactive Mapping
- Real-time visualization of disaster-prone zones
- Interactive evacuation routes and facilities
- Aid distribution point mapping
- Risk level visualization with color-coded zones

### 🚨 Disaster Management
- banjir and longsor risk assessment
- Evacuation route planning
- Emergency facility management
- Aid distribution coordination

### 👥 Multi-Role Access
- **BPBD Administrators**: Full system access and data management
- **BPBD Staff**: Data input and management capabilities
- **Public Users**: View-only access to disaster information

### 📊 Data Management
- CRUD operations for spatial data
- Geospatial data storage using MySQL
- RESTful API for data exchange
- Real-time data filtering and search

## Technology Stack

- **Backend**: Laravel 12 (PHP 8.3+)
- **Frontend**: Laravel Blade templates with TailwindCSS
- **Mapping**: Leaflet.js with OpenStreetMap
- **Database**: MySQL with JSON storage for geospatial data
- **API**: RESTful JSON APIs
- **Authentication**: Laravel's built-in authentication system

## Installation

### Prerequisites
- PHP 8.3 or higher
- Composer
- Node.js and npm
- MySQL 8.0 or higher

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd giscana
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database configuration**
   - Update your `.env` file with database credentials
   - Create a MySQL database for the application

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

## Database Schema

### Core Tables

#### disaster_zones
- Stores polygon data for banjir and longsor risk areas
- Includes risk levels, affected population, and area calculations

#### evacuation_routes
- Contains linestring data for evacuation paths
- Supports different route types (primary, secondary, emergency)

#### evacuation_facilities
- Point data for evacuation centers and shelters
- Includes capacity, contact information, and facility types

#### aid_disasters
- `id` (Primary Key)
- `nama_kecamatan` (string)
- `jumlah_penerima_bantuan` (integer)
- `bantuan_terdistribusi` (integer)
- `is_active` (boolean)
- `last_synced_at` (timestamp)
- `timestamps`

## API Endpoints

### Disaster Zones
- `GET /api/disaster-zones` - List all disaster zones
- `POST /api/disaster-zones` - Create new disaster zone
- `GET /api/disaster-zones/{id}` - Get specific disaster zone
- `PUT /api/disaster-zones/{id}` - Update disaster zone
- `DELETE /api/disaster-zones/{id}` - Delete disaster zone

### Evacuation Routes
- `GET /api/evacuation-routes` - List all evacuation routes
- `POST /api/evacuation-routes` - Create new evacuation route
- `GET /api/evacuation-routes/{id}` - Get specific evacuation route
- `PUT /api/evacuation-routes/{id}` - Update evacuation route
- `DELETE /api/evacuation-routes/{id}` - Delete evacuation route

### Evacuation Facilities
- `GET /api/evacuation-facilities` - List all evacuation facilities
- `POST /api/evacuation-facilities` - Create new evacuation facility
- `GET /api/evacuation-facilities/{id}` - Get specific evacuation facility
- `PUT /api/evacuation-facilities/{id}` - Update evacuation facility
- `DELETE /api/evacuation-facilities/{id}` - Delete evacuation facility

### Aid Distribution Points
- `GET /api/aid-disasters` - List all aid disasters data
- `POST /api/aid-disasters` - Create new aid disaster record
- `GET /api/aid-disasters/{id}` - Get specific aid disaster data
- `PUT /api/aid-disasters/{id}` - Update aid disaster record
- `DELETE /api/aid-disasters/{id}` - Delete aid disaster record

## Usage

### For Public Users
1. Visit the main page to view the interactive map
2. Use filters to view specific disaster types or risk levels
3. Click on map features to view detailed information
4. Use the search functionality to find specific locations

### For BPBD Staff and Administrators
1. Log in to access data management features
2. Use the API endpoints to manage spatial data
3. Update evacuation routes and facility information
4. Monitor aid distribution points

## Development

### Running in Development Mode
```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Vite development server
npm run dev
```

### Building for Production
```bash
npm run build
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is developed for academic purposes as part of a thesis project.

## Contact

For questions or support, please contact the development team.

---

**Giscana** - Enhancing community resilience through geospatial technology