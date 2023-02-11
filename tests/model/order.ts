import { OrderItem } from  './orderitem';

export class Order{
	public id: number;
	public name: string;
	public number: number;
	public description?: string;
	public createdAt: Date;
	public orderItems: OrderItem[];
}