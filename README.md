# WordPress Plugin for Real Estate Management

This WordPress plugin allows you to turn your site into a real estate management system, perfect for real estate agencies and agents.

## Features

- **Property Management**: Easily manage your properties from the WordPress admin panel. The `Inmuebles` custom post type (CPT) registers properties with numerous custom fields.
- **Demand Management**: Register user demands for buying or selling properties using the `Demandas` CPT to handle these requests.
- **Owner Management**: Register property owners using the `Propietarios` CPT.
- **Appointment Management**: Manage appointments through `Citas` CPT related to `Demandas` and `Inmuebles`. They are recorded in the system, and email notifications are sent to administrators and users with `Demandas`.
- **Query Management**: Record queries submitted through frontend forms using a shortcode. Depending on where they are created, a demand type will be created accordingly. It's also possible to create a `Demandas` from a query. Queries are protected by Google reCAPTCHA to prevent spam, and the Google reCAPTCHA API key can be configured from the plugin's options.
- **Google Maps Integration**: Easily configure property maps from a plugin configuration page.
- **Custom Shortcodes**:
  - Contact Forms: Several contact form options are available.
  - Latest Properties: Displays the latest properties created in the system. This shortcode can be inserted on a WordPress page or post.

## Installation

1. Download the ZIP file from this repository.
2. In your WordPress admin panel, navigate to "Plugins" > "Add New".
3. Click on "Upload Plugin" and select the ZIP file you downloaded.
4. Activate the plugin.

## Usage

Once activated, you can access the plugin's features from the WordPress admin menu:

- **Inmuebles**: Add, edit, and delete properties from "Inmuebles" in the admin menu.
- **Demandas**: Manage property demands from "Demands" in the admin menu.
- **Propietarios**: Administer property owners from "Propietarios" in the admin menu.
- **Citas**: Manage appointments related to "Demandas" and "Inmuebles" from "Citas" in the admin menu.
- **Queries**: View and manage queries submitted by users from "Queries" in the admin menu.

## Recommendations

This plugin is recommended for use with the "Inmuebles Theme," a custom WordPress theme developed by me. You can find the theme repository at [this link](https://github.com/jjmontalban/inmuebles-theme).

## Contributing

Contributions are welcome! If you have ideas for improvements, encounter issues, or simply want to collaborate, feel free to open an issue or submit a pull request.

## Support

If you need assistance or have any questions, please open an issue in this repository, and we'll be happy to help.

## License

This project is licensed under the [MIT License](LICENSE).
