export interface User {
  id: number;
  firstName: string;
  lastName: string;
  email: string;
  avatar?: string;
  roles?: string[];
}

export interface Client extends User {
  preferences?: string[];
}

export interface Owner extends User {
  bio?: string;
  notation: number;
}

export interface Message {
  id: number;
  content: string;
  createdAt: string;
  isRead: boolean;
  sender: User;
  client: Client;
  owner: Owner;
  conversation: Conversation | number;
}

export interface Conversation {
  id: number;
  client: Client;
  owner: Owner;
  createdAt: string;
  updatedAt: string;
  lastMessagePreview?: string;
  hasNewMessages: boolean;
  messages?: Message[];
}
