/**
 * sdk
 *
 * SDK to connect to API.
 *
 * @date 25/10/2016
 * @author Mosufy <mosufy@gmail.com>
 * @copyright Copyright (c) Mosufy
 */

import Constant from './../helpers/constant';
import axios from 'axios';

const config = (accessToken = '') => {
  return ({
    headers: {'Authorization': 'Bearer ' + accessToken}
  });
};

/**
 * Generate client access token
 *
 * @returns AxiosPromise
 */
export function generateClientAccessToken() {
  return axios.post(Constant.apiUrl + '/oauth/access_token/client', {
    grant_type: 'client_credentials',
    client_id: Constant.clientId,
    client_secret: Constant.clientSecret,
    scope: 'role.app'
  });
}

/**
 * Generate user access token
 *
 * @param clientAccessToken
 * @param username
 * @param password
 * @returns AxiosPromise
 */
export function generateUserAccessToken(clientAccessToken, username, password) {
  return axios.post(Constant.apiUrl + '/oauth/access_token', {
    grant_type: 'password',
    client_id: Constant.clientId,
    client_secret: Constant.clientSecret,
    username,
    password,
    scope: 'role.user'
  }, config(clientAccessToken));
}

/**
 * Signup
 *
 * @param clientAccessToken
 * @param email
 * @param password
 * @param name
 * @returns AxiosPromise
 */
export function signup(clientAccessToken, email, password, name) {
  return axios.post(Constant.apiUrl + '/account', {
    email,
    password,
    name
  }, config(clientAccessToken));
}

/**
 * Get user data
 *
 * @param accessToken
 * @returns AxiosPromise
 */
export function getUserData(accessToken) {
  return axios.get(Constant.apiUrl + '/account', config(accessToken));
}

/**
 * Refresh existing user access token
 *
 * @param clientAccessToken
 * @param refreshToken
 * @returns AxiosPromise
 */
export function refreshToken(clientAccessToken, refreshToken) {
  return axios.post(Constant.apiUrl + '/oauth/access_token', {
    grant_type: 'refresh_token',
    client_id: Constant.clientId,
    client_secret: Constant.clientSecret,
    refresh_token: refreshToken
  }, config(clientAccessToken));
}

export function getTodos(accessToken) {
  return axios.get(Constant.apiUrl + '/todos', config(accessToken));
}

export function insertTodo(accessToken, text) {
  return axios.post(Constant.apiUrl + '/todos', {
    title: text
  }, config(accessToken));
}

export function toggleTodo(accessToken, id) {
  return axios.put(Constant.apiUrl + '/todos/' + id + '/toggle', null, config(accessToken));
}

export function deleteAllTodos(accessToken) {
  return axios.delete(Constant.apiUrl + '/todos', config(accessToken));
}