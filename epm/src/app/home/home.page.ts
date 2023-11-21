import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AlertController } from '@ionic/angular';

interface UserData {
  nombre: string;
  apellido: string;
  direccion: string;
  numTarjCred: string;
  banco: string;
  fechaVencimiento: string;
  codCvv: number;
  id: number;
}

@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss'],
})
export class HomePage implements OnInit {
  connectionMessage: string = '';
  users: UserData[] = [];
  noUsersMessage: string = '';

  constructor(private http: HttpClient, private alertController: AlertController) {}

  testDatabaseConnection() {
    this.http.get('http://localhost/ionic-backend/save-data.php').subscribe(
      (response: any) => {
        console.log('Conexión exitosa a la base de datos.');
      },
      (error) => {
        console.error('Error de conexión a la base de datos.');
      }
    );
  }
  

  getUsers() {
    this.http.get('http://localhost/ionic-backend/save-data.php?action=getUsers').subscribe(
      (response: any) => {
        if (response.users) {
          this.users = response.users;
        } else {
          this.noUsersMessage = 'No existen usuarios en la base de datos.';
        }
      },
      (error) => {
        console.error('Error al obtener la lista de usuarios.');
      }
    );
  }

  ngOnInit() {
    this.getUsers();
  }

  async deleteUser(id: number) {
    const alert = await this.alertController.create({
      header: 'Confirmar eliminación',
      message: '¿Estás seguro de que deseas eliminar este usuario?',
      buttons: [
        {
          text: 'Cancelar',
          role: 'cancel',
          cssClass: 'secondary',
        }, {
          text: 'Eliminar',
          handler: () => {
            this.http.get(`http://localhost/ionic-backend/save-data.php?action=deleteUser&id=${id}`).subscribe(
              (response: any) => {
                if (response?.success) {
                  console.log('Usuario eliminado con éxito');
                  this.getUsers();
                } else {
                  console.error('Error al eliminar usuario');
                }
              },
              (error) => {
                console.error('Error de conexión');
              }
            );
          }
        }
      ]
    });

    await alert.present();
  }

  async editUser(userId: number, user: UserData) {
    const alert = await this.alertController.create({
      header: 'Editar Usuario',
      inputs: [
        {
          name: 'nombre',
          type: 'text',
          value: user.nombre,
          placeholder: 'Nuevo Nombre'
        },
        {
          name: 'apellido',
          type: 'text',
          value: user.apellido,
          placeholder: 'Nuevo Apellido'
        },
        {
          name: 'direccion',
          type: 'text',
          value: user.direccion,
          placeholder: 'Nueva Dirección'
        },
        {
          name: 'numTarjCred',
          type: 'text',
          value: user.numTarjCred,
          placeholder: 'Nuevo Número de Tarjeta de Crédito'
        },
        {
          name: 'banco',
          type: 'text',
          value: user.banco,
          placeholder: 'Nuevo Banco'
        },
        {
          name: 'fechaVencimiento',
          type: 'text',
          value: user.fechaVencimiento,
          placeholder: 'Nueva Fecha de Vencimiento'
        },
        {
          name: 'codCvv',
          type: 'number',
          value: user.codCvv,
          placeholder: 'Nuevo CVV'
        },
      ],
      buttons: [
        {
          text: 'Cancelar',
          role: 'cancel',
          handler: () => {
            console.log('Edición cancelada');
          }
        },
        {
          text: 'Editar',
          handler: async (data) => {
            if (this.areFieldsValid(data)) {
              await this.updateUser(user.id, data);
            } else {
              await this.showValidationAlert();
            }
          }
        }
      ]
    });

    await alert.present();
  }


  areFieldsValid(data: any): boolean {
    const onlyLetters = /^[A-Za-z\s]+$/;
    const dateFormat = /^20(23|2[4-9]|[3-9]\d)-[0-1]\d$/;
    const nameFormat = /^[A-Za-z]{1,15}$/;
    const cardNumberFormat = /^[\d\s]{12,16}$/;


    return (
      nameFormat.test(data.nombre.trim()) &&
      nameFormat.test(data.apellido.trim()) &&
      onlyLetters.test(data.banco.trim()) &&
      data.banco.length <= 20 &&
      data.direccion.trim() !== '' &&
      cardNumberFormat.test(data.numTarjCred.trim()) &&
      data.numTarjCred.trim() !== '' &&
      dateFormat.test(data.fechaVencimiento.trim()) &&
      data.fechaVencimiento.trim().length == 7 &&
      data.codCvv.trim() !== '' &&
      !data.codCvv.includes('-') &&
      data.codCvv.length == 3
    );
  }
  


  async showValidationAlert() {
    const alert = await this.alertController.create({
      header: 'Campos Obligatorios',
      message: 'Por favor, ingresa datos validos.',
      buttons: ['OK']
    });

    await alert.present();
  }


  async updateUser(userId: number, newData: UserData) {
    this.http.post('http://localhost/ionic-backend/save-data.php?action=updateUser', { id: userId, data: newData }).subscribe(
      (response: any) => {
        console.log('Response:', response);
        if (response?.success) {
          console.log('Usuario actualizado con éxito');
          this.getUsers();
        } else {
          console.error('Error al actualizar usuario');
        }
      },
      (error) => {
        console.error('Error de conexión');
      }
    );
  }
  
}
