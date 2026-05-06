import React, { createContext, useContext, useState, useEffect } from 'react';
import { me } from '../api/auth';

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
}

interface AuthContextType {
  user: User | null;
  token: string | null;
  login: (t: string, u: User) => void;
  logout: () => void;
  isAuthenticated: boolean;
}

const AuthContext = createContext<AuthContextType>({} as AuthContextType);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [token, setToken] = useState<string | null>(localStorage.getItem('token'));

  useEffect(() => {
    if (token && !user) {
      me()
        .then(r => setUser(r.data.data))
        .catch(() => {
          setToken(null);
          localStorage.removeItem('token');
        });
    }
  }, [token]);

  const login = (t: string, u: User) => {
    setToken(t);
    setUser(u);
    localStorage.setItem('token', t);
  };

  const logout = () => {
    setToken(null);
    setUser(null);
    localStorage.removeItem('token');
  };

  return (
    <AuthContext.Provider value={{ user, token, login, logout, isAuthenticated: !!token && !!user }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
