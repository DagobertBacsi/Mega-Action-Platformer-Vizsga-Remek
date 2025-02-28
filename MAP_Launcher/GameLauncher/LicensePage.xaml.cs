using GameLauncher;
using Microsoft.Data.SqlClient;
using MySql.Data.MySqlClient;
using System;
using System.Collections.Generic;
using System.Text;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Shapes;

namespace MAP_Launcher
{
    /// <summary>
    /// Interaction logic for LincensePage.xaml
    /// </summary>
    public partial class LincensePage : Window
    {
        private readonly string connectionString = "Server=localhost;Database=db_mega;Uid=root;Pwd=;";
        public LincensePage()
        {
            InitializeComponent();
        }
        private void CloseButton_Click(object sender, RoutedEventArgs e)
        {
            Application.Current.Shutdown();
        }

        // Licensz ellenőrző gomb eseménye
        private void BtnValidate_Click(object sender, RoutedEventArgs e)
        {
            string licenseKey = LicenseKeyTextBox.Text.Trim();

            if (IsValidLicense(licenseKey))
            {
                MessageBox.Show("Licensz érvényes! Továbbítás a főablakhoz...", "Siker", MessageBoxButton.OK, MessageBoxImage.Information);

                // MainWindow megnyitása
                MainWindow mainWindow = new MainWindow();
                mainWindow.Show();

                // Jelenlegi ablak bezárása
                this.Close();
            }
            else
            {
                MessageBox.Show("Érvénytelen licensz kulcs!", "Hiba", MessageBoxButton.OK, MessageBoxImage.Error);
            }
        }

        // Licenszkulcs ellenőrzése az adatbázisban
        private bool IsValidLicense(string licenseKey)
        {
            string query = "SELECT COUNT(*) FROM Licensz WHERE license_key = @licenseKey";

            try
            {
                using (MySqlConnection conn = new MySqlConnection(connectionString))
                {
                    conn.Open();
                    using (MySqlCommand cmd = new MySqlCommand(query, conn))
                    {
                        cmd.Parameters.AddWithValue("@licenseKey", licenseKey);
                        int count = Convert.ToInt32(cmd.ExecuteScalar());
                        return count > 0; // Ha van találat, akkor érvényes a kulcs
                    }
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show("Hiba történt az adatbázis elérésekor: " + ex.Message, "Hiba", MessageBoxButton.OK, MessageBoxImage.Error);
                return false;
            }
        }
    }
}
