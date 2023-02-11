import { Order } from  './order';

export class OrderItem{
	public id: number;
	public name: string;
	public parentOrder: Order;
}