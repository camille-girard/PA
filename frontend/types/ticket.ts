export interface Ticket {
  id: number;
  title: string;
  description: string;
  status: string;
  priority: string;
  createdAt: string;
  updatedAt?: string;
  messages?: TicketMessage[];
  client?: {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
  };
}

export interface TicketMessage {
  id: number;
  content: string;
  createdAt: string;
  sender: {
    id: number;
    firstName: string;
    lastName: string;
  };
}
